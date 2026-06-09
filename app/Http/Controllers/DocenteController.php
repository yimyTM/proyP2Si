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

    // ── CU04: Importación masiva de usuarios ─────────────────────────────────

    /** Muestra el formulario de importación. */
    public function importar(): View
    {
        return view('admin.importar_docentes');
    }

    /**
     * Procesa el archivo CSV o XLSX y crea cuentas de usuario.
     * Columnas requeridas: nombre, apellido, ci, correo, telefono, rol
     * Roles válidos: Docente, Coordinador, Autoridades (o Autoridad)
     */
    public function importarStore(ImportDocenteRequest $request): View|RedirectResponse
    {
        $archivo   = $request->file('archivo');
        $extension = strtolower($archivo->getClientOriginalExtension());

        if (! in_array($extension, ['csv', 'txt', 'xlsx'], true)) {
            return back()->withErrors(['archivo' => 'Formato no permitido. Use CSV o Excel.']);
        }

        $filas = $extension === 'xlsx'
            ? $this->parsearXlsx($archivo->getRealPath())
            : $this->parsearCsv($archivo->getRealPath());

        if (empty($filas)) {
            return back()->withErrors(['archivo' => 'El archivo no contiene registros para procesar.']);
        }

        // Encabezados (primera fila)
        $encabezados = array_map(fn($h) => strtolower(trim((string) $h)), $filas[0]);

        $requeridas  = ['nombre', 'apellido', 'ci', 'correo', 'telefono', 'rol'];
        $faltantes   = array_values(array_diff($requeridas, $encabezados));

        if (! empty($faltantes)) {
            return back()->withErrors([
                'archivo' => 'Columnas faltantes en el archivo: ' . implode(', ', $faltantes) . '.',
            ]);
        }

        // Índices de cada columna
        $idx = array_flip($encabezados);

        // Filas de datos (sin encabezado)
        $dataFilas = array_slice($filas, 1);
        $dataFilas = array_filter($dataFilas, fn($f) => count(array_filter($f, 'strlen')) > 0);

        if (empty($dataFilas)) {
            return back()->withErrors(['archivo' => 'El archivo no contiene registros para procesar.']);
        }

        $exitosos     = [];
        $errores      = [];
        $rolesValidos = ['Docente', 'Coordinador', 'Autoridades'];
        $numFila      = 2;

        foreach ($dataFilas as $fila) {
            $nombre    = trim((string) ($fila[$idx['nombre']]    ?? ''));
            $apellido  = trim((string) ($fila[$idx['apellido']]  ?? ''));
            $ci        = trim((string) ($fila[$idx['ci']]        ?? ''));
            $correo    = strtolower(trim((string) ($fila[$idx['correo']]   ?? '')));
            $telefono  = trim((string) ($fila[$idx['telefono']]  ?? ''));
            $rolRaw    = trim((string) ($fila[$idx['rol']]       ?? ''));

            // Normalizar rol: "Autoridad" → "Autoridades", capitalizar
            $rol = match (strtolower($rolRaw)) {
                'docente'      => 'Docente',
                'coordinador'  => 'Coordinador',
                'autoridades', 'autoridad' => 'Autoridades',
                default        => $rolRaw,
            };

            // Validar campos obligatorios
            if (empty($nombre) || empty($apellido) || empty($ci)) {
                $errores[] = "Fila {$numFila}: nombre, apellido y CI son obligatorios.";
                $numFila++;
                continue;
            }

            // Validar rol
            if (! in_array($rol, $rolesValidos, true)) {
                $errores[] = "Fila {$numFila}: CI '{$ci}' — Rol no válido ('{$rolRaw}'). Use: Docente, Coordinador o Autoridades.";
                $numFila++;
                continue;
            }

            // Validar unicidad de CI
            $ciDuplicado = Docente::where('ci', $ci)->exists()
                        || User::where('ci', $ci)->exists();
            if ($ciDuplicado) {
                $errores[] = "Fila {$numFila}: CI '{$ci}' ya existe en el sistema (duplicado).";
                $numFila++;
                continue;
            }

            // Validar unicidad de correo
            if ($correo && User::where('correo', $correo)->exists()) {
                $errores[] = "Fila {$numFila}: correo '{$correo}' ya está registrado (duplicado).";
                $numFila++;
                continue;
            }

            // Coordinador/Autoridades requieren correo para poder iniciar sesión
            if ($rol !== 'Docente' && empty($correo)) {
                $errores[] = "Fila {$numFila}: CI '{$ci}' — El rol {$rol} requiere correo electrónico.";
                $numFila++;
                continue;
            }

            try {
                $passwordPlano = null;

                if ($rol === 'Docente') {
                    $docente = Docente::create([
                        'nombre'      => $nombre,
                        'apellido'    => $apellido,
                        'ci'          => $ci,
                        'correo'      => $correo ?: null,
                        'nroTelefono' => $telefono,
                    ]);
                    if ($correo) {
                        $passwordPlano = CuentaProvisionaService::crearCuentaDocente($docente);
                    }
                } else {
                    $passwordPlano = CuentaProvisionaService::crearCuentaPersonal(
                        $nombre, $apellido, $ci, $correo, $telefono, $rol
                    );
                }

                $exitosos[] = [
                    'nombre'   => "{$nombre} {$apellido}",
                    'ci'       => $ci,
                    'correo'   => $correo ?: '(sin correo)',
                    'rol'      => $rol,
                    'password' => $passwordPlano ?? '(sin cuenta — sin correo)',
                ];
            } catch (\Throwable) {
                $errores[] = "Fila {$numFila}: CI '{$ci}' — Error interno al crear la cuenta.";
            }

            $numFila++;
        }

        BitacoraService::registrar(
            "Importación masiva de personal: " . count($exitosos) . " creados, " . count($errores) . " errores."
        );

        return view('admin.importar_resultado', compact('exitosos', 'errores'));
    }

    /** Descarga la plantilla CSV con columnas requeridas. */
    public function plantilla(): Response
    {
        $contenido  = "nombre,apellido,ci,correo,telefono,rol\n";
        $contenido .= "Juan,Pérez,12345678,jperez@ficct.edu.bo,70012345,Docente\n";
        $contenido .= "María,González,87654321,mgonzalez@ficct.edu.bo,71109876,Coordinador\n";
        $contenido .= "Carlos,Rodríguez,11223344,crodriguez@ficct.edu.bo,72211234,Autoridades\n";

        return response($contenido, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_importacion_personal.csv"',
        ]);
    }

    // ── Helpers privados ──────────────────────────────────────────────────────

    /** Parsea un archivo CSV y devuelve array de filas. */
    private function parsearCsv(string $path): array
    {
        $rows   = [];
        $handle = fopen($path, 'r');
        if ($handle === false) return [];

        // Descartar BOM UTF-8 si existe
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $rows[] = array_map('strval', $row);
        }
        fclose($handle);

        return $rows;
    }

    /** Parsea un archivo XLSX usando ZipArchive + SimpleXML sin dependencias externas. */
    private function parsearXlsx(string $path): array
    {
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) return [];

        // Cargar cadenas compartidas (celdas de tipo texto)
        $sharedStrings = [];
        $ssContent     = $zip->getFromName('xl/sharedStrings.xml');
        if ($ssContent !== false) {
            $ss = simplexml_load_string($ssContent);
            foreach ($ss->si as $si) {
                if (isset($si->t)) {
                    $sharedStrings[] = (string) $si->t;
                } else {
                    $text = '';
                    foreach ($si->r ?? [] as $r) {
                        $text .= (string) ($r->t ?? '');
                    }
                    $sharedStrings[] = $text;
                }
            }
        }

        // Cargar primera hoja
        $sheetContent = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        if ($sheetContent === false) return [];

        $rows = [];
        $xml  = simplexml_load_string($sheetContent);

        foreach ($xml->sheetData->row as $row) {
            $cells   = [];
            $lastCol = 0;

            foreach ($row->c as $cell) {
                preg_match('/^([A-Z]+)/', (string) $cell['r'], $m);
                $colIdx = $this->colLetterToIndex($m[1] ?? 'A');

                while ($lastCol < $colIdx - 1) {
                    $cells[] = '';
                    $lastCol++;
                }

                $value = '';
                if (isset($cell->v)) {
                    $value = (string) $cell['t'] === 's'
                        ? ($sharedStrings[(int) $cell->v] ?? '')
                        : (string) $cell->v;
                }
                $cells[] = $value;
                $lastCol = $colIdx;
            }
            $rows[] = $cells;
        }

        return $rows;
    }

    /** Convierte letras de columna Excel (A, B, AA…) a índice 1-based. */
    private function colLetterToIndex(string $col): int
    {
        $idx = 0;
        foreach (str_split(strtoupper($col)) as $c) {
            $idx = $idx * 26 + (ord($c) - 64);
        }
        return $idx;
    }
}
