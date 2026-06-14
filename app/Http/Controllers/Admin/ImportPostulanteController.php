<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gestion;
use App\Models\Grupo;
use App\Models\Inscripcion;
use App\Models\Modalidad;
use App\Models\Postulante;
use App\Models\Turno;
use App\Models\User;
use App\Services\BitacoraService;
use App\Services\SpreadsheetParser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ImportPostulanteController extends Controller
{
    /** Capacidad máxima fija por grupo. */
    private const CUPO_GRUPO = 70;

    /** Estados de inscripción que cuentan como "inscrito activo" a distribuir. */
    private const ESTADOS_ACTIVOS = ['Validado', 'Habilitado', 'Asignado a grupo'];

    // ── Paso 0: formulario de carga ───────────────────────────────────────────

    public function form(): View
    {
        $gestionActiva = Gestion::where('estado', 'Abierta')->first();

        return view('admin.importar_postulantes', compact('gestionActiva'));
    }

    /** Plantilla CSV de postulantes. */
    public function plantilla(): Response
    {
        $contenido  = "nombre,apellido,ci,telefono,sexo,fecha_nacimiento,ciudad,colegio\n";
        $contenido .= "Juan,Pérez Gómez,12345678,70011223,M,2006-03-15,Santa Cruz,Colegio Nacional\n";
        $contenido .= "María,Lopez Vaca,87654321,71122334,F,2005-11-02,Santa Cruz,Colegio La Salle\n";

        return response($contenido, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_postulantes.csv"',
        ]);
    }

    // ── Paso 1: procesar archivo → registrar inscritos → preview ──────────────

    public function procesar(Request $request): View|RedirectResponse
    {
        $gestionActiva = Gestion::where('estado', 'Abierta')->first();
        if (! $gestionActiva) {
            return back()->with('error', 'Debe existir una gestión académica abierta para registrar postulantes.');
        }

        $request->validate([
            'archivo' => ['required', 'file', 'mimes:csv,txt,xlsx', 'max:5120'],
        ], [
            'archivo.required' => 'Seleccione un archivo para importar.',
            'archivo.mimes'    => 'El archivo debe ser CSV o XLSX.',
        ]);

        $archivo = $request->file('archivo');
        $filas   = SpreadsheetParser::parse($archivo->getRealPath(), $archivo->getClientOriginalExtension());

        if (empty($filas)) {
            return back()->withErrors(['archivo' => 'El archivo está vacío o no se pudo leer.']);
        }

        // Encabezados
        $encabezados = array_map(fn ($h) => strtolower(trim((string) $h)), $filas[0]);
        $requeridas  = ['nombre', 'apellido', 'ci'];
        $faltantes   = array_values(array_diff($requeridas, $encabezados));
        if (! empty($faltantes)) {
            return back()->withErrors([
                'archivo' => 'Columnas faltantes: ' . implode(', ', $faltantes) . '. Use la plantilla oficial.',
            ]);
        }

        $idx       = array_flip($encabezados);
        $dataFilas = array_slice($filas, 1);
        $dataFilas = array_filter($dataFilas, fn ($f) => count(array_filter($f, 'strlen')) > 0);

        if (empty($dataFilas)) {
            return back()->withErrors(['archivo' => 'El archivo no contiene registros para procesar.']);
        }

        $exitosos = [];
        $errores  = [];
        $numFila  = 2;

        DB::transaction(function () use ($dataFilas, $idx, $gestionActiva, &$exitosos, &$errores, &$numFila) {
            foreach ($dataFilas as $fila) {
                $get      = fn ($k) => trim((string) ($fila[$idx[$k]] ?? ''));
                $nombre   = $get('nombre');
                $apellido = $get('apellido');
                $ci       = $get('ci');

                if ($nombre === '' || $apellido === '' || $ci === '') {
                    $errores[] = "Fila {$numFila}: nombre, apellido y CI son obligatorios.";
                    $numFila++;
                    continue;
                }

                if (Postulante::where('ci', $ci)->exists() || User::where('ci', $ci)->exists()) {
                    $errores[] = "Fila {$numFila}: CI '{$ci}' ya existe en el sistema (duplicado).";
                    $numFila++;
                    continue;
                }

                $sexo = strtoupper($get('sexo'));
                $sexo = in_array($sexo, ['M', 'F'], true) ? $sexo : null;

                $postulante = Postulante::create([
                    'nombre'              => $nombre,
                    'apellidos'           => $apellido,
                    'ci'                  => $ci,
                    'nroTelefono'         => $get('telefono') ?: null,
                    'sexo'                => $sexo,
                    'fecha_nacimiento'    => $this->parseFecha($get('fecha_nacimiento')),
                    'ciudad'              => $get('ciudad') ?: null,
                    'colegio_procedencia' => $get('colegio') ?: null,
                    'estado'              => 'activo',
                ]);

                // Registrar como inscrito (validado) en la gestión activa.
                Inscripcion::create([
                    'fecha'     => now()->toDateString(),
                    'estado'    => 'Habilitado',
                    'idPost'    => $postulante->idPost,
                    'idGestion' => $gestionActiva->idGestion,
                ]);

                $exitosos[] = ['nombre' => "{$nombre} {$apellido}", 'ci' => $ci];
                $numFila++;
            }
        });

        BitacoraService::registrar(
            "Carga masiva de postulantes: " . count($exitosos) . " inscritos, " . count($errores) . " errores (gestión {$gestionActiva->idGestion})."
        );

        // ── Cálculo de grupos sobre el estado actual de la gestión ────────────
        $resumen = $this->calcularGrupos($gestionActiva);

        return view('admin.importar_postulantes_preview', array_merge($resumen, [
            'gestion'     => $gestionActiva,
            'exitosos'    => $exitosos,
            'errores'     => $errores,
            'modalidades' => Modalidad::orderBy('nombModalidad')->get(),
            'turnos'      => Turno::orderBy('idTurno')->get(),
        ]));
    }

    // ── Paso 2: ACEPTAR → crear grupos faltantes + distribuir ─────────────────

    public function confirmar(Request $request): RedirectResponse
    {
        $gestionActiva = Gestion::where('estado', 'Abierta')->first();
        if (! $gestionActiva) {
            return redirect()->route('admin.grupos.index')->with('error', 'No hay una gestión activa.');
        }

        $data = $request->validate([
            'codeModalidad' => ['required', 'integer', 'exists:modalidads,codeModalidad'],
            'idTurno'       => ['required', 'integer', 'exists:turnos,idTurno'],
        ]);

        $resumen = $this->calcularGrupos($gestionActiva);

        if ($resumen['totalInscritos'] === 0) {
            return redirect()->route('admin.grupos.index')->with('error', 'No hay inscritos para distribuir.');
        }

        $resultado = DB::transaction(function () use ($gestionActiva, $data, $resumen) {
            // 1. Crear los grupos faltantes (capacidad fija 70).
            $nombres = $this->generarNombresGrupo($gestionActiva->idGestion, $resumen['gruposACrear']);
            foreach ($nombres as $nombre) {
                Grupo::create([
                    'numero_grupo'  => $nombre,
                    'capacidad'     => self::CUPO_GRUPO,
                    'codeModalidad' => $data['codeModalidad'],
                    'idTurno'       => $data['idTurno'],
                    'idGestion'     => $gestionActiva->idGestion,
                ]);
            }

            // 2. Distribuir TODOS los inscritos round-robin entre los grupos.
            $grupos = Grupo::where('idGestion', $gestionActiva->idGestion)
                ->orderBy('numero_grupo')
                ->get();

            $inscritos = Inscripcion::where('idGestion', $gestionActiva->idGestion)
                ->whereIn('estado', self::ESTADOS_ACTIVOS)
                ->whereHas('postulante')
                ->with('postulante')
                ->get()
                ->sortBy('postulante.apellidos')
                ->values();

            foreach ($inscritos as $i => $insc) {
                $grupo = $grupos[$i % $grupos->count()];
                $insc->update(['codigoG' => $grupo->codigoG, 'estado' => 'Asignado a grupo']);
            }

            return ['grupos' => $grupos->count(), 'inscritos' => $inscritos->count(), 'creados' => count($nombres)];
        });

        BitacoraService::registrar(
            "Carga masiva: {$resultado['creados']} grupos creados; {$resultado['inscritos']} inscritos distribuidos en {$resultado['grupos']} grupos (gestión {$gestionActiva->idGestion})."
        );

        return redirect()->route('admin.grupos.index')->with(
            'success',
            "Listo: {$resultado['inscritos']} inscritos distribuidos en {$resultado['grupos']} grupos " .
            "({$resultado['creados']} creados ahora)."
        );
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    /**
     * Calcula el estado de grupos de la gestión:
     *  - total inscritos activos
     *  - grupos necesarios = ceil(total / 70)
     *  - grupos existentes / a crear
     *  - reparto estimado por grupo
     */
    private function calcularGrupos(Gestion $gestion): array
    {
        $totalInscritos = Inscripcion::where('idGestion', $gestion->idGestion)
            ->whereIn('estado', self::ESTADOS_ACTIVOS)
            ->whereHas('postulante')
            ->count();

        $gruposExistentes = Grupo::where('idGestion', $gestion->idGestion)->count();
        $gruposNecesarios = (int) ceil($totalInscritos / self::CUPO_GRUPO);
        $gruposACrear     = max(0, $gruposNecesarios - $gruposExistentes);
        $gruposFinales    = max($gruposNecesarios, $gruposExistentes);

        // Reparto estimado (round-robin equilibrado)
        $base  = $gruposFinales > 0 ? intdiv($totalInscritos, $gruposFinales) : 0;
        $resto = $gruposFinales > 0 ? $totalInscritos % $gruposFinales : 0;

        return [
            'totalInscritos'   => $totalInscritos,
            'gruposExistentes' => $gruposExistentes,
            'gruposNecesarios' => $gruposNecesarios,
            'gruposACrear'     => $gruposACrear,
            'gruposFinales'    => $gruposFinales,
            'porGrupoMin'      => $base,
            'porGrupoMax'      => $resto > 0 ? $base + 1 : $base,
        ];
    }

    /** Genera N nombres de grupo únicos (A, B, …, Z, AA…) no usados en la gestión. */
    private function generarNombresGrupo(int $idGestion, int $cantidad): array
    {
        if ($cantidad <= 0) {
            return [];
        }

        $usados  = Grupo::where('idGestion', $idGestion)->pluck('numero_grupo')->all();
        $nombres = [];
        $n       = 0;

        while (count($nombres) < $cantidad) {
            $candidato = $this->indiceALetras($n);
            if (! in_array($candidato, $usados, true) && ! in_array($candidato, $nombres, true)) {
                $nombres[] = $candidato;
            }
            $n++;
        }

        return $nombres;
    }

    /** 0→A, 1→B, …, 25→Z, 26→AA … */
    private function indiceALetras(int $n): string
    {
        $s = '';
        $n++; // 1-based
        while ($n > 0) {
            $n--;
            $s = chr(65 + ($n % 26)) . $s;
            $n = intdiv($n, 26);
        }
        return $s;
    }

    /** Convierte una fecha de texto a Y-m-d o null. */
    private function parseFecha(string $valor): ?string
    {
        $valor = trim($valor);
        if ($valor === '') {
            return null;
        }
        try {
            return \Carbon\Carbon::parse($valor)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
