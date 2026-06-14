<?php

namespace App\Services;

use App\Models\Gestion;
use App\Models\Grupo;
use App\Models\Modalidad;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * CU09 – Apertura automática de grupos mixtos.
 *
 * Los postulantes NO se separan por carrera durante el CUP: todos toman las
 * mismas materias. La carrera elegida solo importa para la admisión (CU13).
 * Los grupos se separan por MODALIDAD (Presencial / Virtual).
 *
 * Algoritmo:
 *   grupos_por_modalidad = ⌈inscritos_validados_modalidad ÷ capacidad_por_grupo⌉
 *   capacidad de cada grupo = distribuida equitativamente
 */
class AperturaGruposService
{
    /**
     * Calcula cuántos grupos se crearían SIN persistir nada.
     * Útil para mostrar el preview en el formulario.
     *
     * @return array<int, array{modalidad: string, inscritos: int, numGrupos: int}>
     */
    public static function preview(int $idGestion, int $capacidadPorGrupo): array
    {
        $resultado = [];
        foreach (Modalidad::all() as $modalidad) {
            $inscritos = self::contarInscritos($idGestion, $modalidad->codeModalidad);
            $resultado[] = [
                'codeModalidad' => $modalidad->codeModalidad,
                'modalidad'     => $modalidad->nombModalidad,
                'inscritos'     => $inscritos,
                'numGrupos'     => $capacidadPorGrupo > 0 ? (int) ceil($inscritos / $capacidadPorGrupo) : 0,
            ];
        }
        return $resultado;
    }

    /**
     * Ejecuta el algoritmo y persiste los grupos en la BD.
     *
     * @return array{grupos_creados: Collection, resumen: array, error?: string}
     */
    public static function calcularYAbrir(Gestion $gestion, int $capacidadPorGrupo): array
    {
        if ($capacidadPorGrupo <= 0) {
            return [
                'grupos_creados' => collect(),
                'resumen'        => [],
                'error'          => 'La capacidad por grupo debe ser mayor a 0.',
            ];
        }

        $modalidades   = Modalidad::all();
        $gruposCreados = collect();
        $resumen       = [];

        DB::transaction(function () use ($gestion, $capacidadPorGrupo, $modalidades, &$gruposCreados, &$resumen) {

            $contador = Grupo::where('idGestion', $gestion->idGestion)->count();

            foreach ($modalidades as $modalidad) {

                $totalInscritos = self::contarInscritos($gestion->idGestion, $modalidad->codeModalidad);

                if ($totalInscritos === 0) {
                    $resumen[] = [
                        'modalidad'        => $modalidad->nombModalidad,
                        'capacidadPorGrupo'=> $capacidadPorGrupo,
                        'inscritos'        => 0,
                        'numGrupos'        => 0,
                        'grupos'           => [],
                        'mensaje'          => 'Sin inscritos validados en esta modalidad — no se creó ningún grupo.',
                    ];
                    continue;
                }

                $numGrupos     = (int) ceil($totalInscritos / $capacidadPorGrupo);
                $resto         = $totalInscritos % $capacidadPorGrupo; // alumnos en el último grupo
                $gruposDeModal = [];

                for ($i = 1; $i <= $numGrupos; $i++) {
                    $contador++;
                    $numeroGrupo = 'G-' . str_pad($contador, 2, '0', STR_PAD_LEFT);

                    $grupo = Grupo::create([
                        'numero_grupo'  => $numeroGrupo,
                        'capacidad'     => $capacidadPorGrupo,
                        'codeModalidad' => $modalidad->codeModalidad,
                        'idGestion'     => $gestion->idGestion,
                    ]);

                    $gruposCreados->push($grupo);
                    $gruposDeModal[] = [
                        'codigoG'      => $grupo->codigoG,
                        'numero_grupo' => $numeroGrupo,
                        'capacidad'    => $capacidadPorGrupo,
                    ];
                }

                $resumen[] = [
                    'modalidad'        => $modalidad->nombModalidad,
                    'capacidadPorGrupo'=> $capacidadPorGrupo,
                    'inscritos'        => $totalInscritos,
                    'numGrupos'        => $numGrupos,
                    'grupos'           => $gruposDeModal,
                    'mensaje'          => null,
                ];
            }
        });

        return [
            'grupos_creados' => $gruposCreados,
            'resumen'        => $resumen,
        ];
    }

    /**
     * Total de inscritos validados en una gestión para una modalidad dada.
     * Se cuenta por inscripción única (un postulante = una inscripción).
     */
    private static function contarInscritos(int $idGestion, int $codeModalidad): int
    {
        return DB::table('inscripcions as i')
            ->join('carrera__inscritos as ci', 'ci.idInscripcion', '=', 'i.idInscripcion')
            ->join('carreras as c', 'c.codCarrera', '=', 'ci.codCarrera')
            ->where('i.idGestion', $idGestion)
            ->where('i.estado', 'Validado')
            ->where('c.codeModalidad', $codeModalidad)
            ->where('ci.prioridad', 1)
            ->distinct('i.idInscripcion')
            ->count('i.idInscripcion');
    }
}
