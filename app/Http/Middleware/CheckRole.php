<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware RBAC: verifica que el usuario autenticado posea
 * al menos uno de los roles requeridos por la ruta.
 *
 * Uso en rutas:
 *   Route::middleware('role:Administrador')        → solo AD
 *   Route::middleware('role:Administrador,Docente') → AD o D
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $rolUsuario = Auth::user()->rol?->nombre_Rol;

        if (! in_array($rolUsuario, $roles, true)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
