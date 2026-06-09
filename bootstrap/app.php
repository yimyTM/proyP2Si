<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias del middleware RBAC
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        /*
         * FIX: ERR_TOO_MANY_REDIRECTS
         * Cuando un usuario ya autenticado intenta acceder a una ruta con
         * middleware 'guest' (p.ej. /login), Laravel necesita saber a dónde
         * redirigirlo. Sin esta configuración, redirige a '/' que a su vez
         * redirige a '/login' → bucle infinito.
         * Solución: apuntar a '/dashboard', una ruta centralizada que
         * decide el destino según el rol del usuario.
         */
        $middleware->redirectUsersTo('/dashboard');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if (! $request->expectsJson()) {
                $redirect = redirect()->route('login');
                // Solo mostrar el mensaje si el usuario tenía una sesión activa (cookie de sesión presente)
                if ($request->hasCookie(config('session.cookie'))) {
                    $redirect = $redirect->withErrors([
                        'session' => 'Su sesión ha expirado. Por favor, inicie sesión nuevamente.',
                    ]);
                }
                return $redirect;
            }
        });
    })->create();
