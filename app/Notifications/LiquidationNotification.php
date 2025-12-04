<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LiquidationNotification extends Notification
{
    use Queueable;

    private $amount;
    private $razon;

    /**
     * Create a new notification instance.
     */
    public function __construct($amount,$razon)
    {
        $this->amount = $amount;
        $this->razon = $razon;
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
                'title' => 'Pago recibido desde '.config('app.name'),
                'msg' => 'Ha recibido un pago de $'.$this->amount.' desde '.config('app.name').' por motivo '.$this->razon,
            ])->subject('Pago recibido.');
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
