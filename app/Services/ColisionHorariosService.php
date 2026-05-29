<?php

namespace App\Services;

use App\Models\Aula;
use App\Models\Docente;
use App\Models\Horario;
use Illuminate\Support\Collection;

class ColisionHorariosService
{
    /**
     * Verifica si un docente tiene colisión de horario con el horario propuesto.
     *
     * @param  Docente  $docente       Docente a asignar.
     * @param  Horario  $horarioNuevo  Horario que se quiere asignar.
     * @param  int|null $excludeGrupo  Grupo a excluir (para ediciones, evitar
     *                                 comparar el grupo consigo mismo).
     * @return array{tiene_colision: bool, detalle: string|null}
     */
    public static function verificarDocenteColision(
        Docente $docente,
        Horario $horarioNuevo,
        ?int $excludeGrupo = null
    ): array {
        // Cargamos todos los horarios de los grupos donde ya está el docente
        $horariosExistentes = self::horariosDelDocente($docente, $excludeGrupo);

        foreach ($horariosExistentes as $horarioExistente) {
            if (self::seSuperponen($horarioExistente, $horarioNuevo)) {
                return [
                    'tiene_colision' => true,
                    'detalle' => sprintf(
                        'El docente %s ya tiene clase el %s de %s a %s (Grupo #%s).',
                        $docente->nombre_completo,
                        $horarioExistente->dia,
                        $horarioExistente->hora_ini->format('H:i'),
                        $horarioExistente->hora_fin->format('H:i'),
                        $horarioExistente->pivot->codigoG ?? '?'
                    ),
                ];
            }
        }

        return ['tiene_colision' => false, 'detalle' => null];
    }

    /**
     * Verifica si un aula tiene colisión de horario con el horario propuesto.
     *
     * @return array{tiene_colision: bool, detalle: string|null}
     */
    public static function verificarAulaColision(
        Aula $aula,
        Horario $horarioNuevo,
        ?int $excludeGrupo = null
    ): array {
        $horariosExistentes = self::horariosDelAula($aula, $excludeGrupo);

        foreach ($horariosExistentes as $horarioExistente) {
            if (self::seSuperponen($horarioExistente, $horarioNuevo)) {
                return [
                    'tiene_colision' => true,
                    'detalle' => sprintf(
                        'El aula #%s ya está ocupada el %s de %s a %s (Grupo #%s).',
                        $aula->idAula,
                        $horarioExistente->dia,
                        $horarioExistente->hora_ini->format('H:i'),
                        $horarioExistente->hora_fin->format('H:i'),
                        $horarioExistente->pivot->codigoG ?? '?'
                    ),
                ];
            }
        }

        return ['tiene_colision' => false, 'detalle' => null];
    }

    /**
     * Verifica todas las colisiones posibles antes de guardar una asignación.
     * Devuelve la lista completa de errores encontrados (puede haber más de uno).
     *
     * @return string[] Lista de mensajes de error (vacía si no hay colisiones).
     */
    public static function verificarTodo(
        Docente $docente,
        Aula $aula,
        Horario $horario,
        ?int $excludeGrupo = null
    ): array {
        $errores = [];

        $colDocente = self::verificarDocenteColision($docente, $horario, $excludeGrupo);
        if ($colDocente['tiene_colision']) {
            $errores[] = $colDocente['detalle'];
        }

        $colAula = self::verificarAulaColision($aula, $horario, $excludeGrupo);
        if ($colAula['tiene_colision']) {
            $errores[] = $colAula['detalle'];
        }

        return $errores;
    }

    // ─── Helpers privados ─────────────────────────────────────────────────────

    /**
     * Regla de solapamiento de intervalos:
     *   A_ini < B_fin  AND  B_ini < A_fin  AND  mismo día.
     */
    private static function seSuperponen(Horario $a, Horario $b): bool
    {
        if ($a->dia !== $b->dia) {
            return false;
        }
        return $a->hora_ini < $b->hora_fin && $b->hora_ini < $a->hora_fin;
    }

    /** Devuelve todos los horarios ya asignados a los grupos de este docente. */
    private static function horariosDelDocente(Docente $docente, ?int $excludeGrupo): Collection
    {
        return $docente->grupos()
            ->when($excludeGrupo, fn($q) => $q->where('grupos.codigoG', '!=', $excludeGrupo))
            ->with('horarios')
            ->get()
            ->flatMap(fn($grupo) => $grupo->horarios);
    }

    /** Devuelve todos los horarios ya asignados a los grupos de este aula. */
    private static function horariosDelAula(Aula $aula, ?int $excludeGrupo): Collection
    {
        return $aula->grupos()
            ->when($excludeGrupo, fn($q) => $q->where('grupos.codigoG', '!=', $excludeGrupo))
            ->with('horarios')
            ->get()
            ->flatMap(fn($grupo) => $grupo->horarios);
    }
}
