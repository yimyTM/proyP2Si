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
    protected BitacoraService $bitacora;

    public function __construct(BitacoraService $bitacora)
    {
        $this->bitacora = $bitacora;
    }

    /**
     * Muestra el formulario de inicio de sesión.
     * Si el usuario ya está autenticado, redirige según su rol.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Procesa la solicitud de inicio de sesión.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $user = User::where('correo', $request->correo)->first();

        if (!$user) {
            return $this->failedLoginResponse($request, null);
        }

        if (!$user->estado) {
            return back()
                ->withErrors(['correo' => 'Cuenta desactivada, contacte al administrador.'])
                ->withInput($request->only('correo'));
        }

        if ($user->bloqueado_hasta && now()->lt($user->bloqueado_hasta)) {
            $minutosRestantes = now()->diffInMinutes($user->bloqueado_hasta);
            return back()
                ->withErrors(['correo' => "Demasiados intentos fallidos. Intente nuevamente en {$minutosRestantes} minutos."])
                ->withInput($request->only('correo'));
        }

        $credentials = $request->only('correo', 'password');
        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            $user->intentos_fallidos++;
            if ($user->intentos_fallidos >= 3) {
                $user->bloqueado_hasta = now()->addMinutes(15);
            }
            $user->save();

            $this->bitacora->registrar(
                "Intento de inicio de sesión fallido para: {$request->correo}. Intentos: {$user->intentos_fallidos}",
                $user->idUsuario
            );

            return $this->failedLoginResponse($request, $user);
        }

        $user = Auth::user();
        $user->intentos_fallidos = 0;
        $user->bloqueado_hasta = null;
        $user->ultimo_acceso = now();
        $user->save();

        $request->session()->regenerate();

        $this->bitacora->registrar('Inicio de sesión exitoso.', $user->idUsuario);

        // Redirigir según el rol
        return $this->redirectByRole($user);
    }

    protected function failedLoginResponse(Request $request, ?User $user): RedirectResponse
    {
        return back()
            ->withErrors(['correo' => 'El correo o la contraseña son incorrectos.'])
            ->withInput($request->only('correo'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            $this->bitacora->registrar('Cierre de sesión.', $user->idUsuario);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Has cerrado sesión correctamente.');
    }

    private function redirectByRole(User $user): RedirectResponse
    {
        $rol = $user->rol?->nombre_Rol;

        return match ($rol) {
            'Administrador', 'Autoridades', 'Coordinador' => redirect()->route('admin.dashboard'),
            'Docente'    => redirect()->route('docente.dashboard'),
            'Postulante' => redirect()->route('postulante.dashboard'),
            default      => abort(403, 'Rol sin panel asignado.'),
        };
    }
}