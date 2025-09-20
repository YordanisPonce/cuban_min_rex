<?php

namespace App\Http\Controllers;

use App\Models\Order;
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
            }
        }
    }

}
