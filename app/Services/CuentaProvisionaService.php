<?php

namespace App\Services;

use App\Models\Docente;
use App\Models\Postulante;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Genera cuentas de usuario provisionales con contraseñas seguras.
 * Usado por CU02 (carga masiva de personal) y CU03 (habilitación de postulante).
 *
 * ─── Reglas de la contraseña generada ───────────────────────────────────────
 *  • Mínimo 8 caracteres
 *  • Al menos 1 mayúscula
 *  • Al menos 1 minúscula
 *  • Al menos 1 dígito
 *  • Al menos 1 carácter especial (!@#$%)
 */
class CuentaProvisionaService
{
    /**
     * Genera una contraseña aleatoria que cumple todos los requisitos de seguridad.
     * Método: garantiza un carácter de cada tipo requerido, luego rellena
     * con caracteres aleatorios del pool completo y mezcla el resultado.
     */
    public static function generarPassword(): string
    {
        $mayusculas = 'ABCDEFGHJKLMNPQRSTUVWXYZ';   // sin I/O confundibles con 0/1
        $minusculas = 'abcdefghjkmnpqrstuvwxyz';     // sin i/l/o
        $digitos    = '23456789';                     // sin 0/1
        $especiales = '!@#$%';

        // Garantizamos un carácter de cada categoría
        $obligatorios = [
            $mayusculas[random_int(0, strlen($mayusculas) - 1)],
            $minusculas[random_int(0, strlen($minusculas) - 1)],
            $digitos[random_int(0, strlen($digitos) - 1)],
            $especiales[random_int(0, strlen($especiales) - 1)],
        ];

        // Relleno aleatorio hasta 10 caracteres totales
        $todos = $mayusculas . $minusculas . $digitos . $especiales;
        for ($i = 4; $i < 10; $i++) {
            $obligatorios[] = $todos[random_int(0, strlen($todos) - 1)];
        }

        shuffle($obligatorios);
        return implode('', $obligatorios);
    }

    /**
     * Crea una cuenta User + vincula al Docente existente.
     * Devuelve la contraseña en texto plano (solo en este momento se conoce).
     */
    public static function crearCuentaDocente(Docente $docente): string
    {
        $password    = self::generarPassword();
        $rolDocente  = Rol::where('nombre_Rol', 'Docente')->firstOrFail();

        $user = User::create([
            'nombreCompleto' => "{$docente->nombre} {$docente->apellido}",
            'correo'         => $docente->correo,
            'telefono'       => $docente->nroTelefono,
            'password'       => Hash::make($password),
            'idRol'          => $rolDocente->idRol,
        ]);

        $docente->update(['idUsuario' => $user->idUsuario]);

        return $password;
    }

    /**
     * Crea una cuenta User para un Postulante ya existente.
     * Devuelve la contraseña en texto plano.
     */
    public static function crearCuentaPostulante(Postulante $postulante): string
    {
        $password      = self::generarPassword();
        $rolPostulante = Rol::where('nombre_Rol', 'Postulante')->firstOrFail();

        $user = User::create([
            'nombreCompleto' => "{$postulante->nombre} {$postulante->apellidos}",
            'correo'         => $postulante->correo,
            'telefono'       => $postulante->nroTelefono,
            'password'       => Hash::make($password),
            'idRol'          => $rolPostulante->idRol,
        ]);

        $postulante->update(['idUsuario' => $user->idUsuario]);

        return $password;
    }

    /** Sincroniza datos del User vinculado o crea cuenta si hay correo y aún no existe. */
    public static function sincronizarCuentaDocente(Docente $docente): ?string
    {
        if (! $docente->correo) {
            return null;
        }

        if ($docente->usuario) {
            $docente->usuario->update([
                'nombreCompleto' => "{$docente->nombre} {$docente->apellido}",
                'correo'         => $docente->correo,
                'telefono'       => $docente->nroTelefono,
            ]);

            return null;
        }

        return self::crearCuentaDocente($docente);
    }

    /** Sincroniza datos del User vinculado o crea cuenta si hay correo y aún no existe. */
    public static function sincronizarCuentaPostulante(Postulante $postulante): ?string
    {
        if (! $postulante->correo) {
            return null;
        }

        if ($postulante->usuario) {
            $postulante->usuario->update([
                'nombreCompleto' => "{$postulante->nombre} {$postulante->apellidos}",
                'correo'         => $postulante->correo,
                'telefono'       => $postulante->nroTelefono,
            ]);

            return null;
        }

        return self::crearCuentaPostulante($postulante);
    }

    /**
     * Crea la cuenta del docente o genera una nueva contraseña si ya existe.
     * Requiere que el docente tenga correo registrado.
     */
    public static function provisionarCuentaDocente(Docente $docente): string
    {
        if (! $docente->correo) {
            throw new \InvalidArgumentException('El docente no tiene correo registrado.');
        }

        if ($docente->usuario) {
            $password = self::generarPassword();
            $docente->usuario->update(['password' => Hash::make($password)]);

            return $password;
        }

        return self::crearCuentaDocente($docente);
    }
}
