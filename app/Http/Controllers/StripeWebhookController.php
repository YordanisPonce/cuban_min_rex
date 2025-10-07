<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\File;
use App\Models\Sale;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\FilePaid;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;

class StripeWebhookController extends CashierController
{
    public function handleCustomerSubscriptionCreated(array $payload)
    {
        $session = $payload['data']['object'];
        $orderId = $session['metadata']['order_id'] ?? null;
        if ($orderId) {
            $order = Order::find($orderId);
            if ($order) {
                $order->status = 'paid';
                $order->paid_at = now();
                $order->expires_at = Carbon::now()->addMonths($order->plan->duration_months);
                $order->save();

                // Asignar plan al usuario
                $user = $order->user;
                $user->current_plan_id = $order->plan_id;
                $user->plan_expires_at = $order->expires_at;
                $user->save();

                $subscripction = new Subscription();
                $subscripction->user_id = $user->id;
                $subscripction->ends_at = $order->expires_at;
                $subscripction->save();
            }
        }
    }

    public function handlePaymentIntentSucceeded(array $payload){
        $session = $payload['data']['object'];
        $file_id = $session['metadata']['file_id'] ?? null;
        $user_id = $session['metadata']['user_id'] ?? null;
        if($file_id && $user_id){
            $file = File::find($file_id);
            $user = User::find($user_id);
            if($file && $user){
                $sale = new Sale();
                $sale->user_id = $user->id;
                $sale->file_id = $file->id;
                $sale->save();

                //Aqui configurar para enviar el correo al cliente
                $user->notify(new FilePaid(route('file.download', $file->id)));
            }
        }
    }
}
