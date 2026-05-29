<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{

    public function __construct(public string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'correo' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $minutos = config('auth.passwords.users.expire', 60);

        return (new MailMessage)
            ->subject('Restablecer contraseña – FICCT')
            ->greeting('Hola, ' . ($notifiable->nombreCompleto ?? 'usuario'))
            ->line('Recibimos una solicitud para restablecer la contraseña de tu cuenta en el Sistema de Admisión FICCT.')
            ->line("Este enlace expira en {$minutos} minutos.")
            ->action('Restablecer contraseña', $url)
            ->line('Si no solicitaste este cambio, ignora este correo. Tu contraseña no se modificará.')
            ->salutation('Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones – UAGRM');
    }
}
