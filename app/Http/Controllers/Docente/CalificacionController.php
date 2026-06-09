<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Exam_Materia;
use App\Models\Examen;
use App\Models\Inscripcion;
use App\Models\Nota;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CalificacionController extends Controller
{
    /** CU11 – Lista de asignaciones (grupo+materia) del docente autenticado. */
    public function index(): View
    {
        $docente = Auth::user()->docente;

        abort_unless($docente, 403, 'Sin perfil de docente.');

        $asignaciones = \App\Models\materi_grupo::where('codigoDoc', $docente->codigoDoc)
            ->with([
                'grupo' => fn($q) => $q->with(['gestion', 'modalidad', 'turno']),
                'materia',
            ])
            ->get();

        // Enriquecer cada asignación con el conteo de estudiantes y exámenes
        foreach ($asignaciones as $asig) {
            $asig->totalEstudiantes = Inscripcion::where('codigoG', $asig->codigoG)->count();

            $gestion = $asig->grupo?->gestion;
            $asig->totalExamenes = $gestion
                ? Exam_Materia::whereHas(
                    'examen',
                    fn($q) => $q->where('idGestion', $gestion->idGestion)
                )->where('idMateria', $asig->idMateria)->count()
                : 0;
        }

        return view('docente.calificaciones.index', compact('docente', 'asignaciones'));
    }

    /** CU11 – Formulario de calificaciones para un grupo+materia específico. */
    public function edit(int $grupo, int $materia): View
    {
        $docente = Auth::user()->docente;
        abort_unless($docente, 403);

        $asignacion = \App\Models\materi_grupo::where('codigoDoc', $docente->codigoDoc)
            ->where('codigoG', $grupo)
            ->where('idMateria', $materia)
            ->with(['grupo.gestion', 'grupo.modalidad', 'grupo.turno', 'materia'])
            ->firstOrFail();

        $gestion     = $asignacion->grupo->gestion;
        $grupoModel  = $asignacion->grupo;
        $materiaModel = $asignacion->materia;

        $periodoAbierto = $gestion && $gestion->estado === 'Abierta';

        // Examenes de la gestión ordenados por parcial
        $examenes = Examen::where('idGestion', $gestion->idGestion)
            ->orderBy('nroParcial')
            ->get();

        // Exam_Materias vinculadas a esta materia
        $examMaterias = collect();
        foreach ($examenes as $examen) {
            $em = Exam_Materia::where('idExamen', $examen->idExamen)
                ->where('idMateria', $materia)
                ->first();
            if ($em) {
                $em->setRelation('examen', $examen);
                $examMaterias->push($em);
            }
        }

        $totalPonderacion = $examMaterias->sum(fn($em) => (float) $em->examen->ponderacion);

        // Estudiantes inscritos en este grupo
        $inscripciones = Inscripcion::where('codigoG', $grupo)
            ->with('postulante')
            ->get()
            ->sortBy(fn($i) => $i->postulante?->apellidos);

        // Mapa de notas existentes: [idInscripcion][idEx_materia] => Nota|null
        $notasMap = [];
        $allIdExMaterias = $examMaterias->pluck('idEx_materia')->all();
        $allIdInscripciones = $inscripciones->pluck('idInscripcion')->all();

        $notasExistentes = Nota::whereIn('idInscripcion', $allIdInscripciones)
            ->whereIn('idEx_materia', $allIdExMaterias)
            ->get();

        foreach ($notasExistentes as $n) {
            $notasMap[$n->idInscripcion][$n->idEx_materia] = $n;
        }

        return view('docente.calificaciones.edit', compact(
            'docente', 'grupoModel', 'materiaModel', 'gestion',
            'examMaterias', 'inscripciones', 'notasMap',
            'periodoAbierto', 'totalPonderacion'
        ));
    }

    /** CU11 – Guarda o actualiza calificaciones. */
    public function update(Request $request, int $grupo, int $materia): RedirectResponse
    {
        $docente = Auth::user()->docente;
        abort_unless($docente, 403);

        // Verificar que el docente está asignado a este grupo+materia
        $asignacion = \App\Models\materi_grupo::where('codigoDoc', $docente->codigoDoc)
            ->where('codigoG', $grupo)
            ->where('idMateria', $materia)
            ->with(['grupo.gestion', 'materia'])
            ->firstOrFail();

        $gestion = $asignacion->grupo->gestion;

        if (! $gestion || $gestion->estado !== 'Abierta') {
            return back()->with('error',
                'El período de evaluación ha concluido. Las calificaciones están bloqueadas.'
            );
        }

        $request->validate([
            'notas'       => 'array',
            'notas.*'     => 'array',
            'notas.*.*'   => 'nullable|numeric|min:0|max:100',
            'motivo'      => 'nullable|string|max:500',
        ]);

        $notasInput = $request->input('notas', []);
        $motivo     = trim($request->input('motivo', ''));

        // Detectar si alguna nota existente será modificada
        $hayModificacion = false;
        foreach ($notasInput as $idInscripcion => $exams) {
            foreach ($exams as $idExMateria => $calificacion) {
                if ($calificacion === null || $calificacion === '') {
                    continue;
                }
                $notaExistente = Nota::where('idInscripcion', $idInscripcion)
                    ->where('idEx_materia', $idExMateria)
                    ->value('calificacion');
                if ($notaExistente !== null && (float) $notaExistente !== (float) $calificacion) {
                    $hayModificacion = true;
                    break 2;
                }
            }
        }

        if ($hayModificacion && $motivo === '') {
            return back()->withInput()->withErrors([
                'motivo' => 'Debe ingresar el motivo de modificación antes de guardar los cambios.',
            ]);
        }

        $guardadas   = 0;
        $modificadas = 0;

        foreach ($notasInput as $idInscripcion => $exams) {
            foreach ($exams as $idExMateria => $calificacion) {
                if ($calificacion === null || $calificacion === '') {
                    continue;
                }

                $notaExistente = Nota::where('idInscripcion', $idInscripcion)
                    ->where('idEx_materia', $idExMateria)
                    ->first();

                if ($notaExistente) {
                    $anterior = (float) $notaExistente->calificacion;
                    $nueva    = (float) $calificacion;

                    if ($anterior !== $nueva) {
                        $notaExistente->update(['calificacion' => $nueva]);

                        BitacoraService::registrar(
                            "CU11 – Modificación de nota: Inscripción #{$idInscripcion}, " .
                            "ExMateria #{$idExMateria}. " .
                            "Anterior: {$anterior}, Nueva: {$nueva}. " .
                            "Motivo: {$motivo}"
                        );
                        $modificadas++;
                    }
                } else {
                    Nota::create([
                        'idInscripcion' => $idInscripcion,
                        'idEx_materia'  => $idExMateria,
                        'calificacion'  => (float) $calificacion,
                    ]);

                    BitacoraService::registrar(
                        "CU11 – Nueva nota: Inscripción #{$idInscripcion}, " .
                        "ExMateria #{$idExMateria}. Calificación: {$calificacion}. " .
                        "Grupo #{$grupo}, Materia #{$materia}."
                    );
                    $guardadas++;
                }
            }
        }

        $total = $guardadas + $modificadas;
        $msg   = "Guardado correctamente: {$guardadas} nota(s) nueva(s), {$modificadas} modificada(s).";

        if ($total === 0) {
            $msg = 'No se detectaron cambios que guardar.';
        }

        return back()->with('success', $msg);
    }
}
