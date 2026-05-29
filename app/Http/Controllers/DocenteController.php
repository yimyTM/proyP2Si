<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocenteRequest;
use App\Http\Requests\ImportDocenteRequest;
use App\Models\Docente;
use App\Models\User;
use App\Services\BitacoraService;
use App\Services\CuentaProvisionaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class DocenteController extends Controller
{
    // ── CU01: Dashboard del Docente ───────────────────────────────────────────

    public function dashboard(): View
    {
        return view('docente.dashboard');
    }

    // ── CRUD Admin ────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $query = Docente::with('usuario')->orderBy('apellido');

        if ($request->filled('q')) {
            $term = $request->q;
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'like', "%{$term}%")
                  ->orWhere('apellido', 'like', "%{$term}%")
                  ->orWhere('ci', 'like', "%{$term}%")
                  ->orWhere('correo', 'like', "%{$term}%");
            });
        }

        $docentes = $query->paginate(15)->withQueryString();

        return view('admin.docentes.index', compact('docentes'));
    }

    public function create(): View
    {
        return view('admin.docentes.create');
    }

    public function store(DocenteRequest $request): RedirectResponse
    {
        $docente = Docente::create($request->validated());

        $passwordPlano = CuentaProvisionaService::sincronizarCuentaDocente($docente);

        BitacoraService::registrar("Docente creado: {$docente->nombre_completo} (CI: {$docente->ci})");

        $mensaje = 'Docente registrado correctamente.';
        if ($passwordPlano) {
            $mensaje .= " Contraseña provisional: {$passwordPlano}";
        }

        return redirect()
            ->route('admin.docentes.show', $docente)
            ->with('success', $mensaje);
    }

    public function show(Docente $docente): View
    {
        $docente->load(['usuario', 'grupos.turno', 'grupos.modalidad']);

        return view('admin.docentes.show', compact('docente'));
    }

    public function edit(Docente $docente): View
    {
        return view('admin.docentes.edit', compact('docente'));
    }

    public function update(DocenteRequest $request, Docente $docente): RedirectResponse
    {
        $docente->update($request->validated());

        $passwordPlano = CuentaProvisionaService::sincronizarCuentaDocente($docente);

        BitacoraService::registrar("Docente actualizado: {$docente->nombre_completo} (CI: {$docente->ci})");

        $mensaje = 'Docente actualizado correctamente.';
        if ($passwordPlano) {
            $mensaje .= " Cuenta creada. Contraseña provisional: {$passwordPlano}";
        }

        return redirect()
            ->route('admin.docentes.show', $docente)
            ->with('success', $mensaje);
    }

    public function destroy(Docente $docente): RedirectResponse
    {
        $nombre = $docente->nombre_completo;
        $user   = $docente->usuario;

        $docente->delete();

        if ($user) {
            $user->delete();
        }

        BitacoraService::registrar("Docente eliminado: {$nombre}");

        return redirect()
            ->route('admin.docentes.index')
            ->with('success', 'Docente eliminado correctamente.');
    }

    /** Crea cuenta o restablece contraseña para docentes con correo. */
    public function provisionarCuenta(Docente $docente): RedirectResponse
    {
        if (! $docente->correo) {
            return back()->with('error', 'El docente no tiene correo registrado.');
        }

        $query = User::where('correo', $docente->correo);
        if ($docente->idUsuario) {
            $query->where('idUsuario', '!=', $docente->idUsuario);
        }
        if ($query->exists()) {
            return back()->with('error', 'Ese correo ya está en uso por otro usuario.');
        }

        $teniaCuenta     = (bool) $docente->usuario;
        $passwordPlano   = CuentaProvisionaService::provisionarCuentaDocente($docente);

        BitacoraService::registrar(
            ($teniaCuenta ? 'Contraseña restablecida' : 'Cuenta creada') .
            " para docente {$docente->nombre_completo} ({$docente->correo})."
        );

        $mensaje = $teniaCuenta
            ? "Contraseña restablecida: {$passwordPlano}"
            : "Cuenta creada. Contraseña provisional: {$passwordPlano}";

        return back()->with('success', $mensaje);
    }

    // ── CU02: Carga Masiva de Personal ────────────────────────────────────────

    /** Muestra el formulario de importación CSV. */
    public function importar(): View
    {
        return view('admin.importar_docentes');
    }

    /**
     * Procesa el CSV y genera cuentas provisionales.
     * Formato esperado: nombre, apellido, ci, correo, nroTelefono, direccion
     */
    public function importarStore(ImportDocenteRequest $request): View|RedirectResponse
    {
        $handle = fopen($request->file('archivo')->getRealPath(), 'r');

        if ($handle === false) {
            return back()->withErrors(['archivo' => 'No se pudo abrir el archivo.']);
        }

        $encabezados = fgetcsv($handle);

        if (! $this->encabezadosValidos($encabezados)) {
            fclose($handle);
            return back()->withErrors([
                'archivo' => 'El CSV no tiene el formato correcto. Use la plantilla descargable.',
            ]);
        }

        $exitosos = [];
        $errores  = [];
        $fila     = 2;

        while (($datos = fgetcsv($handle)) !== false) {
            if (count($datos) < 3) {
                $errores[] = "Fila {$fila}: datos insuficientes, se omite.";
                $fila++;
                continue;
            }

            [$nombre, $apellido, $ci, $correo, $nroTelefono, $direccion] = array_pad($datos, 6, null);
            $nombre   = trim((string) $nombre);
            $apellido = trim((string) $apellido);
            $ci       = trim((string) $ci);
            $correo   = trim((string) $correo);

            if (empty($nombre) || empty($apellido) || empty($ci)) {
                $errores[] = "Fila {$fila}: nombre, apellido y CI son obligatorios.";
                $fila++;
                continue;
            }
            if (Docente::where('ci', $ci)->exists()) {
                $errores[] = "Fila {$fila}: CI '{$ci}' ya existe en el sistema.";
                $fila++;
                continue;
            }
            if ($correo && User::where('correo', $correo)->exists()) {
                $errores[] = "Fila {$fila}: correo '{$correo}' ya está registrado.";
                $fila++;
                continue;
            }

            $docente = Docente::create([
                'nombre'      => $nombre,
                'apellido'    => $apellido,
                'ci'          => $ci,
                'correo'      => $correo ?: null,
                'nroTelefono' => trim((string) ($nroTelefono ?? '')),
                'direccion'   => trim((string) ($direccion ?? '')),
            ]);

            $passwordPlano = null;
            if ($correo) {
                $passwordPlano = CuentaProvisionaService::crearCuentaDocente($docente);
            }

            $exitosos[] = [
                'nombre'   => "{$nombre} {$apellido}",
                'ci'       => $ci,
                'correo'   => $correo ?: 'Sin correo',
                'password' => $passwordPlano ?? '(sin cuenta — no se proveyó correo)',
            ];
            $fila++;
        }

        fclose($handle);

        BitacoraService::registrar(
            "Carga masiva de docentes: " . count($exitosos) . " importados, " . count($errores) . " errores."
        );

        return view('admin.importar_resultado', compact('exitosos', 'errores'));
    }

    /** Descarga la plantilla CSV. */
    public function plantilla(): Response
    {
        $contenido = "nombre,apellido,ci,correo,nroTelefono,direccion\n";
        $contenido .= "Juan,Pérez,12345678,jperez@ejemplo.com,70012345,Av. Principal 123\n";

        return response($contenido, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_docentes.csv"',
        ]);
    }

    // ── Helper privado ────────────────────────────────────────────────────────

    private function encabezadosValidos(?array $enc): bool
    {
        if (empty($enc)) return false;
        $enc = array_map(fn($h) => strtolower(trim($h)), $enc);
        foreach (['nombre', 'apellido', 'ci'] as $req) {
            if (! in_array($req, $enc, true)) return false;
        }
        return true;
    }
}
