<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\File;
use App\Models\Plan;
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
        $userId = $session['metadata']['user_id'] ?? null;
        $planId = $session['metadata']['plan_id'] ?? null;
        Log::info('Payload', $payload);
        if ($orderId && $userId && $planId) {
            $order = Order::find($orderId);
            $user = User::find($userId);
            $plan = Plan::find($planId);
            if ($order && $user && $plan) {
                $order->status = 'paid';
                $order->paid_at = Carbon::now();
                $order->expires_at = Carbon::now()->addMonths($plan->duration_months);
                $order->save();

                $user->current_plan_id = $plan->id;
                $user->plan_expires_at = Carbon::now()->addMonths($plan->duration_months);
                $user->save();

                $subscripction = new Subscription();
                $subscripction->user_id = $user->id;
                $subscripction->ends_at = Carbon::now()->addMonths($plan->duration_months);
                $subscripction->save();
            }
        }
    }

    public function handlePaymentIntentSucceeded(array $payload)
    {
        $session = $payload['data']['object'];
        $file_id = $session['metadata']['file_id'] ?? null;
        $user_id = $session['metadata']['user_id'] ?? null;
        if ($file_id && $user_id) {
            $file = File::find($file_id);
            $user = User::find($user_id);
            if ($file && $user) {
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
