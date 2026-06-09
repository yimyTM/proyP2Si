<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Exam_Materia;
use App\Models\Examen;
use App\Models\Grupo;
use App\Models\Inscripcion;
use App\Models\Nota;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ResultadoController extends Controller
{
    /** CU12 – Lista de grupos con su estado de procesamiento. */
    public function index(): View
    {
        $docente = Auth::user()->docente;
        abort_unless($docente, 403, 'Sin perfil de docente.');

        $gruposIds = \App\Models\materi_grupo::where('codigoDoc', $docente->codigoDoc)
            ->distinct()
            ->pluck('codigoG');

        $grupos = Grupo::whereIn('codigoG', $gruposIds)
            ->with(['gestion', 'modalidad', 'turno', 'inscripciones'])
            ->get();

        foreach ($grupos as $grupo) {
            $grupo->totalEstudiantes = $grupo->inscripciones->count();
            $grupo->totalProcesados  = $grupo->inscripciones->whereNotNull('resultado')->count();
        }

        return view('docente.resultados.index', compact('docente', 'grupos'));
    }

    /** CU12 – Vista previa de resultados para un grupo (antes/después de procesar). */
    public function show(int $grupo): View
    {
        $docente = Auth::user()->docente;
        abort_unless($docente, 403);

        \App\Models\materi_grupo::where('codigoDoc', $docente->codigoDoc)
            ->where('codigoG', $grupo)
            ->firstOrFail();

        $grupoModel = Grupo::with(['gestion', 'modalidad', 'turno'])->findOrFail($grupo);
        $gestion    = $grupoModel->gestion;

        abort_unless($gestion, 404, 'El grupo no tiene gestión académica asignada.');

        $periodoAbierto = $gestion->estado === 'Abierta';

        // Materias del docente en este grupo
        $materiasIds = \App\Models\materi_grupo::where('codigoDoc', $docente->codigoDoc)
            ->where('codigoG', $grupo)
            ->pluck('idMateria');

        // Exámenes de la gestión ordenados por parcial
        $examenes = Examen::where('idGestion', $gestion->idGestion)
            ->orderBy('nroParcial')
            ->get();

        if ($examenes->isEmpty()) {
            return view('docente.resultados.show', [
                'grupoModel'     => $grupoModel,
                'gestion'        => $gestion,
                'periodoAbierto' => $periodoAbierto,
                'examenes'       => $examenes,
                'inscripciones'  => collect(),
                'resultados'     => [],
                'expectedCount'  => 0,
                'sinExamenes'    => true,
            ]);
        }

        // Exam_materias relevantes para este grupo
        $examMaterias = Exam_Materia::whereIn('idMateria', $materiasIds)
            ->whereHas('examen', fn($q) => $q->where('idGestion', $gestion->idGestion))
            ->with('examen')
            ->get();

        $expectedCount   = $examMaterias->count();
        $examMateriasIds = $examMaterias->pluck('idEx_materia');

        // Estudiantes inscritos
        $inscripciones = Inscripcion::where('codigoG', $grupo)
            ->with('postulante')
            ->get()
            ->sortBy(fn($i) => $i->postulante?->apellidos);

        if ($inscripciones->isEmpty()) {
            return view('docente.resultados.show', [
                'grupoModel'     => $grupoModel,
                'gestion'        => $gestion,
                'periodoAbierto' => $periodoAbierto,
                'examenes'       => $examenes,
                'inscripciones'  => $inscripciones,
                'resultados'     => [],
                'expectedCount'  => $expectedCount,
                'sinExamenes'    => false,
            ]);
        }

        $inscripcionesIds = $inscripciones->pluck('idInscripcion');

        // Cargar notas en un solo query
        $notasAll = Nota::whereIn('idInscripcion', $inscripcionesIds)
            ->whereIn('idEx_materia', $examMateriasIds)
            ->get();

        // Calcular resultados por estudiante
        $resultados = [];
        foreach ($inscripciones as $insc) {
            $notasInsc  = $notasAll->where('idInscripcion', $insc->idInscripcion);
            $totalNotas = $notasInsc->count();
            $completo   = $expectedCount > 0 && $totalNotas >= $expectedCount;

            // Promedio por examen (para la columna visual)
            $porExamen = [];
            foreach ($examenes as $ex) {
                $idsEm      = $examMaterias->where('idExamen', $ex->idExamen)->pluck('idEx_materia')->toArray();
                $notasExamen = $notasInsc->whereIn('idEx_materia', $idsEm);
                $porExamen[$ex->idExamen] = $notasExamen->isNotEmpty()
                    ? round($notasExamen->avg('calificacion'), 2)
                    : null;
            }

            // Promedio ponderado final (misma fórmula que CU11)
            $sumW = 0.0;
            $sumV = 0.0;
            foreach ($examMaterias as $em) {
                $nota = $notasInsc->where('idEx_materia', $em->idEx_materia)->first();
                if ($nota) {
                    $pond  = (float) $em->examen->ponderacion;
                    $sumW += $pond;
                    $sumV += (float) $nota->calificacion * $pond;
                }
            }
            $promedio = $sumW > 0 ? round($sumV / $sumW, 2) : null;

            if ($promedio !== null) {
                if ($completo) {
                    $resultado = $promedio >= 60 ? 'Aprobado' : 'Reprobado';
                } else {
                    $resultado = 'Incompleto';
                }
            } else {
                $resultado = null;
            }

            $resultados[$insc->idInscripcion] = [
                'promedio'      => $promedio,
                'resultado'     => $resultado,
                'completo'      => $completo,
                'porExamen'     => $porExamen,
                'totalNotas'    => $totalNotas,
                'guardado'      => $insc->resultado !== null,
            ];
        }

        return view('docente.resultados.show', compact(
            'grupoModel', 'gestion', 'periodoAbierto',
            'examenes', 'inscripciones', 'resultados', 'expectedCount'
        ) + ['sinExamenes' => false]);
    }

    /** CU12 – Procesa y persiste los promedios y resultados de un grupo. */
    public function procesar(Request $request, int $grupo): RedirectResponse
    {
        $docente = Auth::user()->docente;
        abort_unless($docente, 403);

        \App\Models\materi_grupo::where('codigoDoc', $docente->codigoDoc)
            ->where('codigoG', $grupo)
            ->firstOrFail();

        $grupoModel = Grupo::with('gestion')->findOrFail($grupo);
        $gestion    = $grupoModel->gestion;

        if (! $gestion || $gestion->estado !== 'Abierta') {
            return back()->with('error',
                'El período de evaluación ha concluido. No se pueden procesar resultados.'
            );
        }

        $materiasIds = \App\Models\materi_grupo::where('codigoDoc', $docente->codigoDoc)
            ->where('codigoG', $grupo)
            ->pluck('idMateria');

        $examMaterias = Exam_Materia::whereIn('idMateria', $materiasIds)
            ->whereHas('examen', fn($q) => $q->where('idGestion', $gestion->idGestion))
            ->with('examen')
            ->get();

        if ($examMaterias->isEmpty()) {
            return back()->with('error',
                'No existen exámenes configurados para las materias de este grupo.'
            );
        }

        $expectedCount   = $examMaterias->count();
        $examMateriasIds = $examMaterias->pluck('idEx_materia');

        $inscripciones    = Inscripcion::where('codigoG', $grupo)->get();
        $inscripcionesIds = $inscripciones->pluck('idInscripcion');

        $notasAll = Nota::whereIn('idInscripcion', $inscripcionesIds)
            ->whereIn('idEx_materia', $examMateriasIds)
            ->get();

        if ($notasAll->isEmpty()) {
            return back()->with('error',
                'No existen calificaciones registradas para este grupo.'
            );
        }

        $procesados  = 0;
        $incompletos = 0;
        $sinNotas    = 0;

        foreach ($inscripciones as $insc) {
            $notasInsc = $notasAll->where('idInscripcion', $insc->idInscripcion);

            if ($notasInsc->isEmpty()) {
                $sinNotas++;
                continue;
            }

            $totalNotas = $notasInsc->count();
            $completo   = $totalNotas >= $expectedCount;

            $sumW = 0.0;
            $sumV = 0.0;
            foreach ($examMaterias as $em) {
                $nota = $notasInsc->where('idEx_materia', $em->idEx_materia)->first();
                if ($nota) {
                    $pond  = (float) $em->examen->ponderacion;
                    $sumW += $pond;
                    $sumV += (float) $nota->calificacion * $pond;
                }
            }

            $promedio  = $sumW > 0 ? round($sumV / $sumW, 2) : null;
            $resultado = $completo
                ? ($promedio >= 60 ? 'Aprobado' : 'Reprobado')
                : 'Incompleto';

            $insc->update([
                'promedio'  => $promedio,
                'resultado' => $resultado,
            ]);

            BitacoraService::registrar(
                "CU12 – Resultado procesado: Inscripción #{$insc->idInscripcion}, " .
                "Grupo #{$grupo}, Promedio: {$promedio}, Resultado: {$resultado}."
            );

            $procesados++;
            if (! $completo) {
                $incompletos++;
            }
        }

        $msg = "Resultados procesados: {$procesados} estudiante(s).";
        if ($incompletos > 0) {
            $msg .= " ({$incompletos} marcado(s) como 'Incompleto' por calificaciones faltantes.)";
        }
        if ($sinNotas > 0) {
            $msg .= " ({$sinNotas} sin ninguna calificación registrada, omitidos.)";
        }

        return back()->with('success', $msg);
    }
}
