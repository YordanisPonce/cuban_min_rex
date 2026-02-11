<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CUPPaymentNotification extends Notification
{
    use Queueable;

    private Order $order;
    
    /**
     * Create a new notification instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
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
    public function toMail($notifiable)
    {
        return (new MailMessage)
                ->subject('Nuevo Pago en CUP realizado')
                ->greeting('Hola, Administrador!')
                ->line("Se ha realizado un pago CUP desde **{$this->order->phone}**, monto **{$this->order->amount}**, Nro. Transacción: **{$this->order->code}**. Favor de Revisar su tarjeta y proceder a confirmar el pago.");
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
