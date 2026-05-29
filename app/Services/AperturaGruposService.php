<?php

namespace App\Services;

use App\Models\Gestion;
use App\Models\Grupo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * CU09 – Apertura automática de grupos.
 * Usa los cupos configurados por carrera en gestion_carreras (máx. alumnos por grupo).
 */
class AperturaGruposService
{
    /**
     * @return array{grupos_creados: Collection, resumen: array}
     */
    public static function calcularYAbrir(Gestion $gestion, int $turnoId): array
    {
        $gestion->load(['carreras.modalidad']);

        if ($gestion->carreras->isEmpty()) {
            return [
                'grupos_creados' => collect(),
                'resumen'        => [],
                'error'          => 'No hay carreras con cupos configurados para esta gestión.',
            ];
        }

        $gruposCreados = collect();
        $resumen       = [];

        DB::transaction(function () use ($gestion, $turnoId, &$gruposCreados, &$resumen) {

            foreach ($gestion->carreras as $carrera) {
                $capacidadMax = (int) $carrera->pivot->cupos;

                $totalInscritos = self::contarInscritosValidados($gestion->idGestion, $carrera->codCarrera);

                if ($totalInscritos === 0) {
                    $resumen[] = [
                        'carrera'    => $carrera->nombre,
                        'modalidad'  => $carrera->modalidad?->nombModalidad ?? '—',
                        'cuposMax'   => $capacidadMax,
                        'inscritos'  => 0,
                        'numGrupos'  => 0,
                        'grupos'     => [],
                        'mensaje'    => 'Sin inscritos — no se abrió ningún grupo.',
                    ];
                    continue;
                }

                $numGrupos       = (int) ceil($totalInscritos / $capacidadMax);
                $capacidadBase   = (int) floor($totalInscritos / $numGrupos);
                $resto           = $totalInscritos % $numGrupos;
                $gruposDeCarrera = [];

                for ($i = 1; $i <= $numGrupos; $i++) {
                    $capacidadGrupo = ($i <= $resto)
                        ? $capacidadBase + 1
                        : $capacidadBase;

                    $grupo = Grupo::create([
                        'capacidad'     => $capacidadGrupo,
                        'codeModalidad' => $carrera->codeModalidad,
                        'idTurno'       => $turnoId,
                    ]);

                    $gruposCreados->push($grupo);
                    $gruposDeCarrera[] = [
                        'codigoG'   => $grupo->codigoG,
                        'capacidad' => $capacidadGrupo,
                    ];
                }

                $resumen[] = [
                    'carrera'    => $carrera->nombre,
                    'modalidad'  => $carrera->modalidad?->nombModalidad ?? '—',
                    'cuposMax'   => $capacidadMax,
                    'inscritos'  => $totalInscritos,
                    'numGrupos'  => $numGrupos,
                    'grupos'     => $gruposDeCarrera,
                    'mensaje'    => null,
                ];
            }
        });

        return [
            'grupos_creados' => $gruposCreados,
            'resumen'        => $resumen,
        ];
    }

    private static function contarInscritosValidados(int $idGestion, int $codCarrera): int
    {
        return DB::table('carrera__inscritos as ci')
            ->join('inscripcions as i', 'i.idInscripcion', '=', 'ci.idInscripcion')
            ->where('i.idGestion', $idGestion)
            ->where('i.estado', 'validada')
            ->where('ci.codCarrera', $codCarrera)
            ->where('ci.prioridad', 1)
            ->count();
    }
}
