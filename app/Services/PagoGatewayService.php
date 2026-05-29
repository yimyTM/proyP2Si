<?php

namespace App\Services;

use App\Models\Pago;
use App\Models\Postulante;

/**
 * Simula la consulta a la pasarela de pagos externa.
 *
 * En producción, este servicio haría una llamada HTTP al endpoint
 * del banco/plataforma de cobros para verificar el estado del pago
 * usando el CI del postulante como identificador primario.
 *
 * Por ahora busca en la tabla `pagos` (que en producción estaría
 * sincronizada desde la pasarela real vía webhook o cron).
 */
class PagoGatewayService
{
    /**
     * Consulta el estado de pago de un postulante por su CI.
     *
     * @return array{encontrado: bool, aprobado: bool, pago: Pago|null, postulante: Postulante|null}
     */
    public static function consultarPorCI(string $ci): array
    {
        $postulante = Postulante::where('ci', $ci)->first();

        if (! $postulante) {
            return [
                'encontrado'  => false,
                'aprobado'    => false,
                'pago'        => null,
                'postulante'  => null,
            ];
        }

        $pago = Pago::where('idPost', $postulante->idPost)
            ->where('estado', 'aprobado')
            ->latest('fecha')
            ->first();

        return [
            'encontrado'  => true,
            'aprobado'    => $pago !== null,
            'pago'        => $pago,
            'postulante'  => $postulante,
        ];
    }
}
