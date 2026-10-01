<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GenericNotification extends Notification
{
    use Queueable;

    private $titulo = '';
    private $mensaje = '';
    private $nombre = '';
    private $email = '';
    private $boton_url = '';
    private $boton_texto = 'Click aquí';
    private $nota = '';

    /**
     * Create a new notification instance.
     */
    public function __construct(
        string $titulo,
        string $mensaje,
        string $nombre,
        string $email,
        string $boton_url = '',
        string $boton_texto = 'Click aquí',
        string $nota = ''
    )
    {
        $this->titulo = $titulo;
        $this->mensaje = $mensaje;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->boton_url = $boton_url;
        $this->boton_texto = $boton_texto;
        $this->nota = $nota;
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
            ->view('emails.notification', [
                'titulo' => $this->titulo,
                'mensaje' => $this->mensaje,
                'nombre' => $this->nombre,
                'email' => $this->email,
                'boton_url' => $this->boton_url,
                'boton_texto' => $this->boton_texto,
                'nota' => $this->nota,
            ])->subject($this->titulo);
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
