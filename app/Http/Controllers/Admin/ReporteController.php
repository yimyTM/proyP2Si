<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gestion;
use App\Models\Grupo;
use App\Services\ResultadoAcademicoService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteController extends Controller
{
    public function __construct(private ResultadoAcademicoService $resultado) {}

    /** Catálogo de reportes disponibles (clave => título). */
    private const REPORTES = [
        'lista-general'        => 'Lista general de postulantes',
        'aprobados'            => 'Postulantes aprobados',
        'reprobados'           => 'Postulantes reprobados',
        'promedios'            => 'Promedios generales',
        'grupos-habilitados'   => 'Cantidad de grupos habilitados',
        'estadisticas-materia' => 'Estadísticas por materia',
        'docentes-grupos'      => 'Docentes por grupos',
        'grupos-aprobados'     => 'Grupos con mayor cantidad de aprobados',
    ];

    /** Reportes que admiten el filtro por grupo. */
    private const FILTRABLES_POR_GRUPO = ['lista-general', 'aprobados', 'reprobados', 'promedios'];

    // ── Panel ─────────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $gestiones    = Gestion::orderByDesc('fecha_ini')->get();
        $gestionModel = $this->resolverGestion($request, $gestiones);

        $reporteClave = $this->resolverReporte($request);
        $grupoId      = $request->integer('grupo') ?: null;

        $grupos  = $gestionModel
            ? Grupo::where('idGestion', $gestionModel->idGestion)->orderBy('numero_grupo')->get()
            : collect();

        $reporte = $gestionModel
            ? $this->construirReporte($gestionModel, $reporteClave, $grupoId)
            : null;

        return view('admin.reportes.index', [
            'gestiones'    => $gestiones,
            'gestionModel' => $gestionModel,
            'reportes'     => self::REPORTES,
            'reporteClave' => $reporteClave,
            'grupos'       => $grupos,
            'grupoId'      => $grupoId,
            'filtraGrupo'  => in_array($reporteClave, self::FILTRABLES_POR_GRUPO, true),
            'reporte'      => $reporte,
        ]);
    }

    // ── Exportar CSV ──────────────────────────────────────────────────────────

    public function export(Request $request): StreamedResponse
    {
        $gestiones    = Gestion::orderByDesc('fecha_ini')->get();
        $gestionModel = $this->resolverGestion($request, $gestiones);
        abort_unless($gestionModel, 404, 'Gestión no encontrada.');

        $clave   = $this->resolverReporte($request);
        $grupoId = $request->integer('grupo') ?: null;
        $reporte = $this->construirReporte($gestionModel, $clave, $grupoId);

        $slug  = $clave;
        $fecha = now()->format('Y-m-d');

        return response()->streamDownload(function () use ($reporte, $gestionModel) {
            $h = fopen('php://output', 'w');
            fwrite($h, "\xEF\xBB\xBF"); // BOM UTF-8 para Excel

            fputcsv($h, [$reporte['titulo']]);
            fputcsv($h, ['Gestión:', $gestionModel->nombre]);
            fputcsv($h, ['Generado:', now()->format('d/m/Y H:i')]);
            foreach ($reporte['resumen'] as $label => $valor) {
                fputcsv($h, [$label, $valor]);
            }
            fputcsv($h, []);

            fputcsv($h, $reporte['columnas']);
            foreach ($reporte['filas'] as $fila) {
                fputcsv($h, $fila);
            }
            fclose($h);
        }, "{$slug}_{$gestionModel->idGestion}_{$fecha}.csv", [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // ── Exportar PDF (dompdf) ─────────────────────────────────────────────────

    public function pdf(Request $request): Response
    {
        $gestiones    = Gestion::orderByDesc('fecha_ini')->get();
        $gestionModel = $this->resolverGestion($request, $gestiones);
        abort_unless($gestionModel, 404, 'Gestión no encontrada.');

        $clave   = $this->resolverReporte($request);
        $grupoId = $request->integer('grupo') ?: null;
        $reporte = $this->construirReporte($gestionModel, $clave, $grupoId);

        $pdf = Pdf::loadView('admin.reportes.pdf', [
            'gestionModel' => $gestionModel,
            'reporte'      => $reporte,
        ])->setPaper('a4', 'portrait');

        $fecha = now()->format('Y-m-d');

        return $pdf->download("{$clave}_{$gestionModel->idGestion}_{$fecha}.pdf");
    }

    // ── Resolución de filtros ─────────────────────────────────────────────────

    private function resolverGestion(Request $request, $gestiones): ?Gestion
    {
        $id = $request->integer('gestion');
        if ($id) {
            return $gestiones->firstWhere('idGestion', $id);
        }
        return $gestiones->firstWhere('estado', 'Abierta') ?? $gestiones->first();
    }

    private function resolverReporte(Request $request): string
    {
        $clave = $request->string('reporte')->toString();
        return array_key_exists($clave, self::REPORTES) ? $clave : array_key_first(self::REPORTES);
    }

    // ── Construcción normalizada de cada reporte ──────────────────────────────

    /**
     * Devuelve un reporte normalizado:
     *  ['clave','titulo','columnas'=>[], 'filas'=>[[...]], 'resumen'=>[label=>val], 'estadoCol'=>?int]
     */
    private function construirReporte(Gestion $g, string $clave, ?int $grupoId): array
    {
        return match ($clave) {
            'aprobados'            => $this->repPostulantes($g, $grupoId, 'Aprobado'),
            'reprobados'           => $this->repPostulantes($g, $grupoId, 'Reprobado'),
            'promedios'            => $this->repPromedios($g, $grupoId),
            'grupos-habilitados'   => $this->repGruposHabilitados($g),
            'estadisticas-materia' => $this->repEstadisticasMateria($g),
            'docentes-grupos'      => $this->repDocentesGrupos($g),
            'grupos-aprobados'     => $this->repGruposAprobados($g),
            default                => $this->repListaGeneral($g, $grupoId),
        };
    }

    /** Evaluación académica de la gestión, ordenada por apellido, con filtro opcional de grupo. */
    private function evaluacion(Gestion $g, ?int $grupoId)
    {
        $eval = $this->resultado->evaluarGestion($g->idGestion)->values()
            ->sortBy(fn ($e) => $e->inscripcion->postulante?->apellidos)
            ->values();

        if ($grupoId) {
            $eval = $eval->filter(fn ($e) => (int) $e->inscripcion->codigoG === $grupoId)->values();
        }
        return $eval;
    }

    private function repListaGeneral(Gestion $g, ?int $grupoId): array
    {
        $eval = $this->evaluacion($g, $grupoId);

        $filas = $eval->map(function ($e) {
            $p = $e->inscripcion->postulante;
            return [
                trim(($p?->apellidos ?? '') . ' ' . ($p?->nombre ?? '')) ?: '—',
                $p?->ci ?? '—',
                $e->inscripcion->grupo?->numero_grupo ?? 'Sin grupo',
                $e->promedio ?? '—',
                $e->resultado ?? 'Cursando',
            ];
        })->all();

        return [
            'clave'     => 'lista-general',
            'titulo'    => self::REPORTES['lista-general'],
            'columnas'  => ['Apellidos y Nombre', 'CI', 'Grupo', 'Promedio', 'Estado'],
            'estadoCol' => 4,
            'filas'     => $filas,
            'resumen'   => [
                'Total postulantes' => $eval->count(),
                'Aprobados'         => $eval->where('resultado', 'Aprobado')->count(),
                'Reprobados'        => $eval->where('resultado', 'Reprobado')->count(),
                'Cursando'          => $eval->whereIn('resultado', [null, 'Incompleto'])->count(),
            ],
        ];
    }

    private function repPostulantes(Gestion $g, ?int $grupoId, string $estado): array
    {
        $eval  = $this->evaluacion($g, $grupoId)->where('resultado', $estado)->values();
        $clave = $estado === 'Aprobado' ? 'aprobados' : 'reprobados';

        $filas = $eval->map(function ($e) {
            $p = $e->inscripcion->postulante;
            return [
                trim(($p?->apellidos ?? '') . ' ' . ($p?->nombre ?? '')) ?: '—',
                $p?->ci ?? '—',
                $e->inscripcion->grupo?->numero_grupo ?? 'Sin grupo',
                $e->promedio ?? '—',
            ];
        })->all();

        return [
            'clave'     => $clave,
            'titulo'    => self::REPORTES[$clave],
            'columnas'  => ['Apellidos y Nombre', 'CI', 'Grupo', 'Promedio'],
            'estadoCol' => null,
            'filas'     => $filas,
            'resumen'   => ['Total' => $eval->count()],
        ];
    }

    private function repPromedios(Gestion $g, ?int $grupoId): array
    {
        $eval = $this->evaluacion($g, $grupoId)
            ->filter(fn ($e) => $e->promedio !== null)
            ->sortByDesc('promedio')
            ->values();

        $filas = $eval->map(function ($e) {
            $p = $e->inscripcion->postulante;
            return [
                trim(($p?->apellidos ?? '') . ' ' . ($p?->nombre ?? '')) ?: '—',
                $p?->ci ?? '—',
                $e->inscripcion->grupo?->numero_grupo ?? 'Sin grupo',
                $e->promedio,
            ];
        })->all();

        $promGeneral = $eval->isNotEmpty() ? round($eval->avg('promedio'), 2) : '—';

        return [
            'clave'     => 'promedios',
            'titulo'    => self::REPORTES['promedios'],
            'columnas'  => ['Apellidos y Nombre', 'CI', 'Grupo', 'Promedio'],
            'estadoCol' => null,
            'filas'     => $filas,
            'resumen'   => [
                'Promedio general'      => $promGeneral,
                'Estudiantes evaluados' => $eval->count(),
            ],
        ];
    }

    private function repGruposHabilitados(Gestion $g): array
    {
        $grupos = Grupo::where('idGestion', $g->idGestion)
            ->with(['modalidad'])
            ->orderBy('numero_grupo')
            ->get();

        $filas = $grupos->map(fn ($gr) => [
            'Grupo ' . $gr->numero_grupo,
            $gr->turno?->nombTurno ?? $gr->turno?->nombre ?? '—',
            $gr->modalidad?->nombModalidad ?? '—',
            $gr->capacidad,
            $gr->inscripciones()->count(),
        ])->all();

        return [
            'clave'     => 'grupos-habilitados',
            'titulo'    => self::REPORTES['grupos-habilitados'],
            'columnas'  => ['Grupo', 'Turno', 'Modalidad', 'Capacidad', 'Inscritos'],
            'estadoCol' => null,
            'filas'     => $filas,
            'resumen'   => ['Grupos habilitados' => $grupos->count()],
        ];
    }

    private function repEstadisticasMateria(Gestion $g): array
    {
        $stats = DB::table('notas as n')
            ->join('exam_materias as em', 'n.idEx_materia', '=', 'em.idEx_materia')
            ->join('examens as e',        'em.idExamen',    '=', 'e.idExamen')
            ->join('materias as m',       'em.idMateria',   '=', 'm.idMateria')
            ->where('e.idGestion', $g->idGestion)
            ->selectRaw('m."nombMateria",
                COUNT(n."idCalif")                                   AS total_notas,
                ROUND(AVG(n.calificacion), 2)                        AS promedio,
                MIN(n.calificacion)                                  AS nota_min,
                MAX(n.calificacion)                                  AS nota_max,
                SUM(CASE WHEN n.calificacion >= 60 THEN 1 ELSE 0 END) AS aprobadas,
                SUM(CASE WHEN n.calificacion <  60 THEN 1 ELSE 0 END) AS reprobadas')
            ->groupBy('m.idMateria', 'm.nombMateria')
            ->orderBy('m.nombMateria')
            ->get();

        $filas = $stats->map(function ($m) {
            $pct = $m->total_notas > 0 ? round($m->aprobadas / $m->total_notas * 100) : 0;
            return [$m->nombMateria, $m->total_notas, $m->promedio, $m->nota_min, $m->nota_max, $m->aprobadas, $m->reprobadas, $pct . '%'];
        })->all();

        return [
            'clave'     => 'estadisticas-materia',
            'titulo'    => self::REPORTES['estadisticas-materia'],
            'columnas'  => ['Materia', 'Calificaciones', 'Promedio', 'Mín', 'Máx', '≥60', '<60', '% aprob.'],
            'estadoCol' => null,
            'filas'     => $filas,
            'resumen'   => ['Materias con notas' => $stats->count()],
        ];
    }

    private function repDocentesGrupos(Gestion $g): array
    {
        $rows = DB::table('materi_grupos as mg')
            ->join('grupos as g',   'mg.codigoG',  '=', 'g.codigoG')
            ->join('docentes as d', 'mg.codigoDoc','=', 'd.codigoDoc')
            ->join('materias as m', 'mg.idMateria','=', 'm.idMateria')
            ->where('g.idGestion', $g->idGestion)
            ->selectRaw('g.numero_grupo, d.nombre, d.apellido, m."nombMateria"')
            ->orderBy('g.numero_grupo')
            ->orderBy('d.apellido')
            ->get();

        $filas = $rows->map(fn ($r) => [
            'Grupo ' . $r->numero_grupo,
            trim("{$r->nombre} {$r->apellido}"),
            $r->nombMateria,
        ])->all();

        return [
            'clave'     => 'docentes-grupos',
            'titulo'    => self::REPORTES['docentes-grupos'],
            'columnas'  => ['Grupo', 'Docente', 'Materia'],
            'estadoCol' => null,
            'filas'     => $filas,
            'resumen'   => ['Asignaciones' => $rows->count()],
        ];
    }

    private function repGruposAprobados(Gestion $g): array
    {
        $eval = $this->evaluacion($g, null);

        $grupos = $eval
            ->filter(fn ($e) => $e->inscripcion->codigoG !== null)
            ->groupBy(fn ($e) => $e->inscripcion->codigoG)
            ->map(function ($items) {
                $grupo = $items->first()->inscripcion->grupo;
                return (object) [
                    'numero_grupo' => $grupo?->numero_grupo ?? '—',
                    'aprobados'    => $items->where('resultado', 'Aprobado')->count(),
                    'reprobados'   => $items->where('resultado', 'Reprobado')->count(),
                    'total'        => $items->count(),
                ];
            })
            ->sortByDesc('aprobados')
            ->values();

        $filas = $grupos->map(fn ($g) => [
            'Grupo ' . $g->numero_grupo,
            $g->aprobados,
            $g->reprobados,
            $g->total,
        ])->all();

        return [
            'clave'     => 'grupos-aprobados',
            'titulo'    => self::REPORTES['grupos-aprobados'],
            'columnas'  => ['Grupo', 'Aprobados', 'Reprobados', 'Total'],
            'estadoCol' => null,
            'filas'     => $filas,
            'resumen'   => ['Grupos evaluados' => $grupos->count()],
        ];
    }
}
