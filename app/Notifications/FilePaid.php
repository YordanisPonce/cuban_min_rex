<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FilePaid extends Notification
{
    use Queueable;

    protected $file_url;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $file_url)
    {
        $this->file_url = $file_url;
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
            ->line('El pago ha sido recibido con éxito. Ahora puede descargar el archivo en el enlace de abajo.')
            ->action('Descargar Archivo', $this->file_url)
            ->line('¡Gracias por usar nuestra aplicación!');
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
