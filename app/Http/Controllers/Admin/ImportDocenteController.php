<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportDocenteRequest;
use App\Models\Docente;
use App\Models\User;
use App\Services\BitacoraService;
use App\Services\CuentaProvisionaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ImportDocenteController extends Controller
{
    /** Muestra el formulario de importación. */
    public function index(): View
    {
        return view('admin.importar_docentes');
    }

    /**
     * CU02 – Procesa el CSV y genera cuentas de acceso provisionales.
     *
     * Formato esperado del CSV (con encabezados):
     *   nombre, apellido, ci, correo, nroTelefono, direccion
     *
     * Flujo por fila:
     *  1. Validar que ci y correo no existan ya en la BD.
     *  2. Crear registro en `docentes`.
     *  3. Generar contraseña provisional segura.
     *  4. Crear User (rol=Docente) + vincular a Docente.
     *  5. Acumular resultado (éxitos y errores) para mostrarlo al admin.
     */
    public function store(ImportDocenteRequest $request): View|RedirectResponse
    {
        $archivo = $request->file('archivo');
        $handle  = fopen($archivo->getRealPath(), 'r');

        if ($handle === false) {
            return back()->withErrors(['archivo' => 'No se pudo abrir el archivo.']);
        }

        // Leer y descartar encabezados
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

            // Validaciones de fila
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

            // Crear el docente sin cuenta todavía
            $docente = Docente::create([
                'nombre'      => $nombre,
                'apellido'    => $apellido,
                'ci'          => $ci,
                'correo'      => $correo ?: null,
                'nroTelefono' => trim((string) ($nroTelefono ?? '')),
                'direccion'   => trim((string) ($direccion ?? '')),
            ]);

            // Generar cuenta solo si tiene correo
            $passwordPlano = null;
            if ($correo) {
                $passwordPlano = CuentaProvisionaService::crearCuentaDocente($docente);
            }

            $exitosos[] = [
                'nombre'    => "{$nombre} {$apellido}",
                'ci'        => $ci,
                'correo'    => $correo ?: 'Sin correo',
                'password'  => $passwordPlano ?? '(sin cuenta — no se proveyó correo)',
            ];

            $fila++;
        }

        fclose($handle);

        BitacoraService::registrar(
            "Carga masiva de docentes: " . count($exitosos) . " importados, " . count($errores) . " errores."
        );

        return view('admin.importar_resultado', compact('exitosos', 'errores'));
    }

    /** Descarga la plantilla CSV con los encabezados correctos. */
    public function descargarPlantilla(): Response
    {
        $contenido = "nombre,apellido,ci,correo,nroTelefono,direccion\n";
        $contenido .= "Juan,Pérez,12345678,jperez@ejemplo.com,70012345,Av. Principal 123\n";
        $contenido .= "María,López,87654321,mlopez@ejemplo.com,76543210,Calle Secundaria 456\n";

        return response($contenido, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_docentes.csv"',
        ]);
    }

    // ─── Helper ───────────────────────────────────────────────────────────────

    private function encabezadosValidos(?array $enc): bool
    {
        if (empty($enc)) {
            return false;
        }
        $requeridos = ['nombre', 'apellido', 'ci'];
        $enc        = array_map(fn($h) => strtolower(trim($h)), $enc);
        foreach ($requeridos as $req) {
            if (! in_array($req, $enc, true)) {
                return false;
            }
        }
        return true;
    }
}
