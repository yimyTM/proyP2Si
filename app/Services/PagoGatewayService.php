<?php

namespace App\Services;

use App\Models\Pago;
use App\Models\Postulante;

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
