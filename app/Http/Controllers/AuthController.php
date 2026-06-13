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


    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

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
            $segundosRestantes = (int) ceil(abs(now()->diffInSeconds($user->bloqueado_hasta)));
            $tiempo = $segundosRestantes >= 60
                ? ceil($segundosRestantes / 60) . ' minuto(s)'
                : $segundosRestantes . ' segundo(s)';

            return back()
                ->withErrors(['correo' => "Demasiados intentos fallidos. Intente nuevamente en {$tiempo}."])
                ->withInput($request->only('correo'));
        }

        $credentials = $request->only('correo', 'password');
        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            $user->intentos_fallidos++;

            // Bloqueo progresivo cada 3 intentos fallidos: 30s → 2min → 15min.
            if ($user->intentos_fallidos % 3 === 0) {
                $nivel    = intdiv($user->intentos_fallidos, 3);
                $segundos = match ($nivel) {
                    1       => 30,    // 1er bloqueo: 30 segundos
                    2       => 120,   // 2do bloqueo: 2 minutos
                    default => 900,   // 3ro en adelante: 15 minutos
                };
                $user->bloqueado_hasta = now()->addSeconds($segundos);
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