<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Muestra el formulario de login.
     * Si ya hay sesión activa, redirige al dashboard del rol correspondiente.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->rol?->nombre_Rol);
        }

        return view('auth.login');
    }

    /**
     * Procesa el intento de inicio de sesión.
     *
     * Flujo:
     *  1. Valida el formulario (LoginRequest).
     *  2. Intenta autenticar con correo + password.
     *  3. Si falla: registra intento fallido en bitácora (si el correo existe)
     *     y devuelve error al formulario.
     *  4. Si tiene éxito: regenera la sesión, registra el evento y redirige
     *     al dashboard del rol del usuario.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = [
            'correo'   => $request->correo,
            'password' => $request->password,
        ];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            // Intento fallido — buscamos si el correo existe para dejar traza
            $usuarioExistente = User::where('correo', $request->correo)->first();

            if ($usuarioExistente) {
                BitacoraService::registrar(
                    "Intento de inicio de sesión fallido para el usuario: {$request->correo}.",
                    $usuarioExistente->idUsuario
                );
            }

            return back()
                ->withErrors(['correo' => 'El correo o la contraseña son incorrectos.'])
                ->withInput($request->only('correo'));
        }

        $request->session()->regenerate();

        BitacoraService::registrar('Inicio de sesión exitoso.');

        return $this->redirectByRole(Auth::user()->rol?->nombre_Rol);
    }

    /**
     * Cierra la sesión del usuario actual.
     */
    public function logout(Request $request): RedirectResponse
    {
        BitacoraService::registrar('Cierre de sesión.');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Has cerrado sesión correctamente.');
    }

    // ─── Helpers ───────────────────────────────────────────────────────────────

    private function redirectByRole(?string $rol): RedirectResponse
    {
        return match ($rol) {
            'Administrador' => redirect()->route('admin.dashboard'),
            'Docente'       => redirect()->route('docente.dashboard'),
            'Postulante'    => redirect()->route('postulante.dashboard'),
            default         => redirect('/'),
        };
    }
}
