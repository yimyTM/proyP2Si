<?php

namespace App\Services;

use App\Models\Exam_Materia;
use App\Models\Inscripcion;
use App\Models\Nota;
use Illuminate\Support\Collection;

class ResultadoAcademicoService
{
    public const NOTA_MINIMA = 60;

    /**
     * Evalúa una colección de notas de un estudiante.
     *
     * @param  Collection  $notas          Notas del estudiante (modelos Nota).
     * @param  int         $esperadas      Cantidad de calificaciones esperadas (exam_materias aplicables).
     * @param  array       $ponderaciones  [idEx_materia => ponderacion] para el promedio ponderado.
     * @return array{promedio: float|null, resultado: string|null, completo: bool, total: int, minima: float|null}
     */
    public function evaluar(Collection $notas, int $esperadas, array $ponderaciones = []): array
    {
        $total = $notas->count();

        if ($total === 0) {
            return ['promedio' => null, 'resultado' => null, 'completo' => false, 'total' => 0, 'minima' => null];
        }

        $minima   = (float) $notas->min('calificacion');
        $completo = $esperadas > 0 && $total >= $esperadas;
        $promedio = $this->promedio($notas, $ponderaciones);

        if ($minima < self::NOTA_MINIMA) {
            $resultado = 'Reprobado';
        } elseif ($completo) {
            $resultado = 'Aprobado';
        } else {
            $resultado = 'Incompleto';
        }

        return compact('promedio', 'resultado', 'completo', 'total', 'minima');
    }

    /** Promedio ponderado por la ponderación del examen; simple si no hay ponderaciones. */
    public function promedio(Collection $notas, array $ponderaciones = []): ?float
    {
        if ($notas->isEmpty()) {
            return null;
        }

        if (! empty($ponderaciones)) {
            $sumW = 0.0;
            $sumV = 0.0;
            foreach ($notas as $n) {
                $p = (float) ($ponderaciones[$n->idEx_materia] ?? 0);
                $sumW += $p;
                $sumV += (float) $n->calificacion * $p;
            }
            if ($sumW > 0) {
                return round($sumV / $sumW, 2);
            }
        }

        return round((float) $notas->avg('calificacion'), 2);
    }

    /**
     * Evalúa TODAS las inscripciones de una gestión a partir de sus notas.
     * Devuelve una colección indexada por idInscripcion con la evaluación + la inscripción.
     *
     * @return Collection<int, object{inscripcion: Inscripcion, promedio: ?float, resultado: ?string, completo: bool, total: int}>
     */
    public function evaluarGestion(int $idGestion): Collection
    {
        // exam_materias de la gestión (define cuántas notas se esperan y sus ponderaciones)
        $examMaterias = Exam_Materia::whereHas('examen', fn ($q) => $q->where('idGestion', $idGestion))
            ->with('examen')
            ->get();

        $esperadas     = $examMaterias->count();
        $ponderaciones = $examMaterias->mapWithKeys(
            fn ($em) => [$em->idEx_materia => (float) ($em->examen->ponderacion ?? 0)]
        )->toArray();

        $inscripciones = Inscripcion::where('idGestion', $idGestion)
            ->with(['postulante', 'grupo'])
            ->get();

        $notasPorInsc = Nota::whereIn('idInscripcion', $inscripciones->pluck('idInscripcion'))
            ->get()
            ->groupBy('idInscripcion');

        return $inscripciones->mapWithKeys(function ($insc) use ($notasPorInsc, $esperadas, $ponderaciones) {
            $notas = $notasPorInsc->get($insc->idInscripcion, collect());
            $eval  = $this->evaluar($notas, $esperadas, $ponderaciones);

            return [$insc->idInscripcion => (object) array_merge($eval, ['inscripcion' => $insc])];
        });
    }
}
