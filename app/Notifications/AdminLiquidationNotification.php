<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminLiquidationNotification extends Notification
{
    use Queueable;

    private $array = [];

    /**
     * Create a new notification instance.
     */
    public function __construct(array $array)
    {
        $this->array = $array;
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
                'title' => 'Pagos realizados desde '.config('app.name'),
                'msg' => 'Se han realizado los siguientes pagos:',
                'array' => $this->array,
            ])->subject('Pagos realizados.');
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
