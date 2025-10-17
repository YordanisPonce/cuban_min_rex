<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GeneratedPassword extends Notification
{
    use Queueable;

    private $password;
    private $name;
    private $email;

    /**
     * Create a new notification instance.
     */
    public function __construct($name, $email, $password)
    {
        $this->password = $password;
        $this->name = $name;
        $this->email = $email;
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
        return (new MailMessage)
            ->subject('Nueva Notificación de Usuario')
            ->greeting("Hola, **{$this->name}**!")
            ->line("Se ha creado una cuenta para este correo (**{$this->email}**) en ".config('app.name').", su contraseña es:")
            ->line("**{$this->password}**");
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
