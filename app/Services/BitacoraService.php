<?php

namespace App\Services;

use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;

/**
 * Servicio transversal de auditoría.
 * Cada CU llama a BitacoraService::registrar() para dejar trazabilidad.
 *
 * Campos que persiste por cada evento:
 *   - descripcion : texto legible del evento (quién hizo qué)
 *   - fecha       : fecha del servidor en el momento del evento
 *   - hora        : hora del servidor en el momento del evento
 *   - direccionIP : IP del cliente (soporta IPv6 con el char(45) de la migración)
 *   - idUsuario   : FK al usuario autenticado (nullable → permite registrar
 *                   intentos de login fallidos sin sesión activa)
 */
class BitacoraService
{
    /**
     * Registra un evento en la bitácora.
     *
     * @param  string   $descripcion  Texto del evento, p. ej. "Inicio de sesión exitoso."
     * @param  int|null $idUsuario    Si se omite, usa Auth::id() (el usuario de la sesión).
     *                                Pasar explícitamente cuando aún no hay sesión (login fallido).
     */
    public static function registrar(string $descripcion, ?int $idUsuario = null): void
    {
        Bitacora::create([
            'descripcion' => $descripcion,
            'fecha'       => now()->toDateString(),
            'hora'        => now()->toTimeString(),
            'direccionIP' => request()->ip() ?? '0.0.0.0',
            'idUsuario'   => $idUsuario ?? Auth::id(),
        ]);
    }
}
