<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gestion;
use App\Models\Grupo;
use App\Models\Inscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteController extends Controller
{
    /** CU14 – Tablero de indicadores institucionales. */
    public function index(Request $request): View
    {
        $gestiones = Gestion::orderByDesc('fecha_ini')->get();

        $gestionId    = $request->integer('gestion');
        $gestionModel = $gestionId
            ? $gestiones->find($gestionId)
            : ($gestiones->firstWhere('estado', 'Abierta') ?? $gestiones->first());

        if (! $gestionModel) {
            return view('admin.reportes.index', compact('gestiones') + ['gestionModel' => null]);
        }

        $indicadores = $this->buildIndicadores($gestionModel);

        $historico = $gestiones
            ->where('estado', 'Cerrada')
            ->values()
            ->map(fn ($g) => $this->buildResumenBasico($g));

        return view('admin.reportes.index', array_merge(
            compact('gestiones', 'gestionModel', 'historico'),
            $indicadores
        ));
    }

    /** CU14 – Exportar reporte en CSV (compatible Excel). */
    public function exportarCsv(int $gestion): StreamedResponse
    {
        $gestionModel = Gestion::findOrFail($gestion);
        $ind          = $this->buildIndicadores($gestionModel);
        $nombre       = $gestionModel->nombre;
        $fecha        = now()->format('Y-m-d');

        return response()->streamDownload(function () use ($nombre, $ind) {
            $h = fopen('php://output', 'w');
            fwrite($h, "\xEF\xBB\xBF"); // UTF-8 BOM para Excel

            fputcsv($h, ["REPORTE INSTITUCIONAL – {$nombre}"]);
            fputcsv($h, ["Generado:", now()->format('d/m/Y H:i')]);
            fputcsv($h, []);

            fputcsv($h, ["RESUMEN GENERAL"]);
            fputcsv($h, ["Total inscritos",          $ind['totalInscritos']]);
            fputcsv($h, ["Aprobados",                 $ind['aprobados']]);
            fputcsv($h, ["Reprobados",                $ind['reprobados']]);
            fputcsv($h, ["En curso / Sin resultado",  $ind['enCurso']]);
            fputcsv($h, []);

            fputcsv($h, ["DISTRIBUCIÓN DE ADMISIÓN POR CARRERA"]);
            fputcsv($h, ["Carrera", "Admitidos", "Reubicados", "Total"]);
            foreach ($ind['admisionPorCarrera'] as $row) {
                fputcsv($h, [
                    $row->nombre ?? 'Sin nombre',
                    $row->admitidos,
                    $row->reubicados,
                    $row->admitidos + $row->reubicados,
                ]);
            }
            fputcsv($h, []);

            fputcsv($h, ["RENDIMIENTO PROMEDIO POR MATERIA"]);
            fputcsv($h, ["Materia", "Promedio (%)", "Calificaciones registradas"]);
            foreach ($ind['promPorMateria'] as $row) {
                fputcsv($h, [
                    $row->nombMateria,
                    number_format($row->pct_promedio ?? 0, 1),
                    $row->total_notas,
                ]);
            }
            fputcsv($h, []);

            fputcsv($h, ["GRUPOS Y OCUPACIÓN"]);
            fputcsv($h, ["Grupo", "Turno", "Capacidad", "Inscritos", "Ocupación (%)"]);
            foreach ($ind['grupos'] as $g) {
                fputcsv($h, [
                    "Grupo {$g->numero_grupo}",
                    $g->turno?->nombre ?? '-',
                    $g->capacidad,
                    $g->totalInscritos,
                    $g->pctOcupacion,
                ]);
            }
            fputcsv($h, []);

            fputcsv($h, ["ASISTENCIA PROMEDIO POR GRUPO"]);
            fputcsv($h, ["Grupo", "Total registros", "Presentes", "Ausentes", "% Asistencia"]);
            foreach ($ind['asistenciaGrupos'] as $row) {
                fputcsv($h, [
                    "Grupo {$row->numero_grupo}",
                    $row->total,
                    $row->presentes,
                    $row->ausentes,
                    number_format($row->pct, 1),
                ]);
            }

            fclose($h);
        }, "reporte_{$nombre}_{$fecha}.csv", [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"reporte_{$nombre}_{$fecha}.csv\"",
        ]);
    }

    /** CU14 – Vista imprimible (sin navegación). */
    public function imprimir(int $gestion): View
    {
        $gestionModel = Gestion::findOrFail($gestion);
        $indicadores  = $this->buildIndicadores($gestionModel);

        return view('admin.reportes.print', array_merge(
            compact('gestionModel'),
            $indicadores
        ));
    }

    /** Construye todos los indicadores para una gestión. */
    private function buildIndicadores(Gestion $gestionModel): array
    {
        $gId = $gestionModel->idGestion;

        // 1. Resumen por resultado académico
        $resultados     = Inscripcion::where('idGestion', $gId)
            ->selectRaw('resultado, COUNT(*) as total')
            ->groupBy('resultado')
            ->pluck('total', 'resultado')
            ->toArray();
        $totalInscritos = Inscripcion::where('idGestion', $gId)->count();
        $aprobados      = (int) ($resultados['Aprobado']  ?? 0);
        $reprobados     = (int) ($resultados['Reprobado'] ?? 0);
        $enCurso        = max(0, $totalInscritos - $aprobados - $reprobados);

        // 2. Distribución de admisión por carrera
        $admisionPorCarrera = DB::table('inscripcions as i')
            ->join('carreras as c', 'i.codCarreraAsignada', '=', 'c.codCarrera')
            ->where('i.idGestion', $gId)
            ->whereIn('i.estado_admision', ['Admitido', 'Reubicado'])
            ->selectRaw('c."codCarrera", c.nombre,
                SUM(CASE WHEN i.estado_admision = \'Admitido\'  THEN 1 ELSE 0 END) AS admitidos,
                SUM(CASE WHEN i.estado_admision = \'Reubicado\' THEN 1 ELSE 0 END) AS reubicados')
            ->groupBy('c.codCarrera', 'c.nombre')
            ->orderByDesc('admitidos')
            ->get();

        // 3. Grupos con ocupación
        $grupos = Grupo::where('idGestion', $gId)->with('turno')->get();
        foreach ($grupos as $g) {
            $g->totalInscritos = Inscripcion::where('codigoG', $g->codigoG)->count();
            $g->pctOcupacion   = $g->capacidad > 0
                ? round($g->totalInscritos / $g->capacidad * 100)
                : 0;
        }

        // 4. Promedio por materia (% sobre puntaje máximo del examen)
        $promPorMateria = DB::table('notas as n')
            ->join('exam_materias as em', 'n.idEx_materia', '=', 'em.idEx_materia')
            ->join('examens as e',        'em.idExamen',    '=', 'e.idExamen')
            ->join('materias as m',       'em.idMateria',   '=', 'm.idMateria')
            ->where('e.idGestion', $gId)
            ->selectRaw('m."idMateria", m."nombMateria",
                AVG(n.calificacion::float / em.puntaje * 100) AS pct_promedio,
                COUNT(n."idCalif") AS total_notas')
            ->groupBy('m.idMateria', 'm.nombMateria')
            ->orderBy('m.idMateria')
            ->get();

        // 5. Asistencia por grupo
        $asistenciaGrupos = DB::table('detalle_asistencias as da')
            ->join('asistencias as a', 'da.idAsistencia', '=', 'a.idAsistencia')
            ->join('grupos as g',      'a.codigoG',       '=', 'g.codigoG')
            ->where('g.idGestion', $gId)
            ->selectRaw('g."codigoG", g.numero_grupo,
                COUNT(*) AS total,
                SUM(CASE WHEN da.estado = \'presente\' THEN 1 ELSE 0 END) AS presentes')
            ->groupBy('g.codigoG', 'g.numero_grupo')
            ->orderBy('g.numero_grupo')
            ->get()
            ->map(function ($row) {
                $row->pct      = $row->total > 0 ? round($row->presentes / $row->total * 100, 1) : 0.0;
                $row->ausentes = $row->total - $row->presentes;
                return $row;
            });

        return compact(
            'totalInscritos', 'aprobados', 'reprobados', 'enCurso',
            'admisionPorCarrera', 'grupos', 'promPorMateria', 'asistenciaGrupos'
        );
    }

    /** Resumen básico de una gestión para la sección comparativa. */
    private function buildResumenBasico(Gestion $g): object
    {
        $gId = $g->idGestion;
        return (object) [
            'gestion'    => $g,
            'inscritos'  => Inscripcion::where('idGestion', $gId)->count(),
            'aprobados'  => Inscripcion::where('idGestion', $gId)->where('resultado', 'Aprobado')->count(),
            'reprobados' => Inscripcion::where('idGestion', $gId)->where('resultado', 'Reprobado')->count(),
            'admitidos'  => Inscripcion::where('idGestion', $gId)->whereIn('estado_admision', ['Admitido', 'Reubicado'])->count(),
        ];
    }
}
