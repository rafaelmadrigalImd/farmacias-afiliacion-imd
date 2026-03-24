<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     */
    public string $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $expirationTime = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->subject('Solicitud de Recuperación de Contraseña - IMD')
            ->greeting('Estimado/a usuario/a:')
            ->line('Hemos recibido una solicitud para restablecer la contraseña de su cuenta en el Portal de Farmacias Afiliadas de IMD.')
            ->line('Para continuar con el proceso de recuperación, haga clic en el siguiente botón:')
            ->action('Restablecer Contraseña', $resetUrl)
            ->line('Este enlace de recuperación expirará en **'.$expirationTime.' minutos** por motivos de seguridad.')
            ->line('**Importante:** Si usted no ha solicitado el cambio de contraseña, no es necesario realizar ninguna acción. Su cuenta permanecerá segura.')
            ->line('Por su seguridad, le recomendamos:')
            ->line('• Crear una contraseña segura que combine letras mayúsculas, minúsculas, números y símbolos')
            ->line('• No compartir su contraseña con terceros')
            ->line('• Cambiar su contraseña periódicamente')
            ->salutation('Atentamente,
Equipo de IMD');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
