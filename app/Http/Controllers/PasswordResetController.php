<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function showLinkRequestForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(ForgotPasswordRequest $request): RedirectResponse
    {
        $status = Password::sendResetLink(
            ['correo' => $request->correo]
        );

        if ($status === Password::RESET_LINK_SENT) {
            BitacoraService::registrar(
                "Solicitud de recuperación de contraseña para: {$request->correo}."
            );

            return back()->with('success', __(
                'Si el correo está registrado, recibirás un enlace para restablecer tu contraseña.'
            ));
        }

        return back()->withErrors(['correo' => __($status)]);
    }

    public function showResetForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token'  => $token,
            'correo' => $request->query('correo', old('correo')),
        ]);
    }

    public function reset(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->only('correo', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => $password])->save();

                BitacoraService::registrar(
                    "Contraseña restablecida mediante recuperación para: {$user->correo}.",
                    $user->idUsuario
                );
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('login')
                ->with('success', 'Contraseña actualizada. Ya puedes iniciar sesión.');
        }

        return back()
            ->withInput($request->only('correo'))
            ->withErrors(['correo' => __($status)]);
    }
}
