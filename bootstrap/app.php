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
            'role'     => \App\Http\Middleware\CheckRole::class,
            'nocache'  => \App\Http\Middleware\NoCacheHeaders::class,
        ]);

        $middleware->appendToGroup('auth', \App\Http\Middleware\NoCacheHeaders::class);

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
        // Sesión expirada (401 → redirigir a login)
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if (! $request->expectsJson()) {
                $redirect = redirect()->route('login');
                if ($request->hasCookie(config('session.cookie'))) {
                    $redirect = $redirect->withErrors([
                        'session' => 'Su sesión ha expirado. Por favor, inicie sesión nuevamente.',
                    ]);
                }
                return $redirect;
            }
        });

        // Token CSRF inválido (419 PAGE EXPIRED → redirigir a login con mensaje)
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if (! $request->expectsJson()) {
                return redirect()->route('login')
                    ->withErrors(['session' => 'La página expiró por seguridad. Vuelva a iniciar sesión.'])
                    ->withInput($request->except('password', '_token'));
            }
        });
    })->create();
