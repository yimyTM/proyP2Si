<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocenteRequest;
use App\Http\Requests\ImportDocenteRequest;
use App\Models\Docente;
use App\Models\Form_Academica;
use App\Models\Gestion;
use App\Models\requisito;
use App\Models\User;
use App\Services\BitacoraService;
use App\Services\CuentaProvisionaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
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
        $formaciones    = Form_Academica::orderBy('nombProfesion')->get();
        $requisitosDoc  = requisito::where('tipo', 'D')->orderBy('idReq')->get();
        $gestionActiva  = Gestion::where('estado', 'Abierta')->first();

        return view('admin.docentes.create', compact('formaciones', 'requisitosDoc', 'gestionActiva'));
    }

    /**
     * CU15 — Registrar docente con su formación académica y requisitos documentales.
     * NO crea cuenta de acceso ni contrato: ambos son pasos posteriores.
     */
    public function store(DocenteRequest $request): RedirectResponse
    {
        // El correo NO se almacena aquí: la tabla docentes no tiene esa columna
        // y la cuenta se crea recién cuando el docente es contratado.
        $datos = collect($request->validated())
            ->only(['nombre', 'apellido', 'ci', 'nroTelefono', 'direccion', 'carga_horaria'])
            ->toArray();

        $docente = DB::transaction(function () use ($request, $datos) {
            $docente = Docente::create($datos);

            // ── Formación académica ────────────────────────────────────────────
            // Formaciones existentes seleccionadas
            $idsFormacion = array_filter((array) $request->input('formaciones', []));

            // Formaciones nuevas (profesiones que el admin escribe en el formulario)
            foreach ((array) $request->input('nuevas_profesiones', []) as $prof) {
                $nombre = trim((string) ($prof['nombProfesion'] ?? ''));
                if ($nombre === '') {
                    continue;
                }
                $nueva = Form_Academica::create([
                    'nroProfesion'  => trim((string) ($prof['nroProfesion'] ?? '')) ?: null,
                    'nombProfesion' => $nombre,
                ]);
                $idsFormacion[] = $nueva->idForm;
            }

            if (! empty($idsFormacion)) {
                $docente->formAcademicas()->sync(array_unique($idsFormacion));
            }

            // ── Requisitos documentales ────────────────────────────────────────
            foreach ((array) $request->input('requisitos', []) as $idReq => $datosReq) {
                $entregado = ! empty($datosReq['entregado']);
                $validado  = ! empty($datosReq['validado']);

                // Solo registramos el requisito si fue al menos entregado o validado.
                if (! $entregado && ! $validado) {
                    continue;
                }

                $docente->requisitosDocente()->create([
                    'idReq'         => $idReq,
                    'entregado'     => $entregado,
                    'validado'      => $validado,
                    'fecha_entrega' => $entregado ? ($datosReq['fecha_entrega'] ?? now()->toDateString()) : null,
                ]);
            }

            return $docente;
        });

        BitacoraService::registrar("Docente registrado (CU15): {$docente->nombre_completo} (CI: {$docente->ci})");

        return redirect()
            ->route('admin.docentes.show', $docente)
            ->with('success', 'Docente registrado correctamente. Valide los requisitos y proceda a contratarlo.');
    }

    public function show(Docente $docente): View
    {
        $docente->load([
            'usuario',
            'grupos.turno',
            'grupos.modalidad',
            'formAcademicas',
            'requisitosDocente.requisito',
        ]);

        $gestionActiva = Gestion::where('estado', 'Abierta')->first();

        $contratado = $gestionActiva
            ? $docente->estaContratadoEn($gestionActiva->idGestion)
            : false;

        $requisitosOk = $docente->tieneRequisitosValidados();

        return view('admin.docentes.show', compact('docente', 'gestionActiva', 'contratado', 'requisitosOk'));
    }

    /**
     * CU15 — Contratar al docente para la gestión activa.
     * Requiere gestión abierta y todos los requisitos documentales validados.
     */
    public function contratar(Docente $docente): RedirectResponse
    {
        $gestionActiva = Gestion::where('estado', 'Abierta')->first();

        if (! $gestionActiva) {
            return back()->with('error', 'No hay una gestión académica abierta para contratar.');
        }

        if ($docente->estaContratadoEn($gestionActiva->idGestion)) {
            return back()->with('error', 'El docente ya está contratado en la gestión activa.');
        }

        if (! $docente->tieneRequisitosValidados()) {
            return back()->with('error', 'No se puede contratar: faltan requisitos documentales por validar.');
        }

        $docente->gestiones()->attach($gestionActiva->idGestion, [
            'fecha_contrato' => now()->toDateString(),
            'estado'         => 'Contratado',
        ]);

        BitacoraService::registrar(
            "Docente contratado: {$docente->nombre_completo} para {$gestionActiva->nombre}."
        );

        return back()->with('success', "Docente contratado para «{$gestionActiva->nombre}». Ahora puede crear su cuenta de acceso.");
    }

    public function edit(Docente $docente): View
    {
        return view('admin.docentes.edit', compact('docente'));
    }

    public function update(DocenteRequest $request, Docente $docente): RedirectResponse
    {
        $datos = collect($request->validated())
            ->only(['nombre', 'apellido', 'ci', 'nroTelefono', 'direccion', 'carga_horaria'])
            ->toArray();

        $docente->update($datos);

        // Si ya tiene cuenta, mantener sincronizado el nombre y teléfono del User.
        if ($docente->usuario) {
            $docente->usuario->update([
                'nombreCompleto' => $docente->nombre_completo,
                'telefono'       => $docente->nroTelefono,
            ]);
        }

        BitacoraService::registrar("Docente actualizado: {$docente->nombre_completo} (CI: {$docente->ci})");

        return redirect()
            ->route('admin.docentes.show', $docente)
            ->with('success', 'Docente actualizado correctamente.');
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

    /**
     * CU15 — Crea la cuenta de acceso del docente (o restablece su contraseña).
     * Solo disponible si el docente ya fue contratado en la gestión activa.
     * El correo se recibe aquí porque la tabla docentes no lo almacena.
     */
    public function provisionarCuenta(Request $request, Docente $docente): RedirectResponse
    {
        $teniaCuenta = (bool) $docente->usuario;

        // Gate: debe estar contratado en la gestión activa antes de tener acceso.
        if (! $teniaCuenta) {
            $gestionActiva = Gestion::where('estado', 'Abierta')->first();
            if (! $gestionActiva || ! $docente->estaContratadoEn($gestionActiva->idGestion)) {
                return back()->with('error', 'Debe contratar al docente antes de crear su cuenta de acceso.');
            }
        }

        // Restablecer contraseña de una cuenta existente (no requiere correo nuevo).
        if ($teniaCuenta) {
            $password = CuentaProvisionaService::provisionarCuentaDocente($docente);
            BitacoraService::registrar("Contraseña restablecida para docente {$docente->nombre_completo} ({$docente->usuario->correo}).");

            return back()->with('success', "Contraseña restablecida: {$password}");
        }

        // Crear cuenta nueva: se necesita un correo válido y único.
        $request->validate([
            'correo' => ['required', 'email', 'max:100', 'unique:users,correo'],
        ], [
            'correo.required' => 'Ingrese el correo para crear la cuenta.',
            'correo.unique'   => 'Ese correo ya está en uso por otro usuario.',
        ]);

        $password = CuentaProvisionaService::crearCuentaDocente($docente, $request->input('correo'));

        BitacoraService::registrar("Cuenta creada para docente {$docente->nombre_completo} ({$request->input('correo')}).");

        return back()->with('success', "Cuenta creada. Correo: {$request->input('correo')} — Contraseña provisional: {$password}");
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
