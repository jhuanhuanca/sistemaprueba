<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserCredentialsNotification extends Notification
{
    use Queueable;

    public function __construct(private string $username, private string $password)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Credenciales de acceso')
            ->line('Sus credenciales de acceso son:')
            ->line('Usuario: ' . $this->username)
            ->line('Contraseña: ' . $this->password)
            ->line('Por favor, cambie su contraseña después del primer inicio de sesión.');
    }
} 