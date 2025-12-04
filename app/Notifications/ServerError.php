<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServerError extends Notification
{
    use Queueable;
    
    private $error;
    private $msg;

    /**
     * Create a new notification instance.
     */
    public function __construct($error,$msg)
    {
        $this->error = $error;
        $this->msg = $msg;
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
            ->view('dinamycnotification', [
                'title' => 'Error durante la ejecución de '.$this->error,
                'msg' => $this->msg,
            ])->subject('¡Contácte con los desarrolladores!');
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
