<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $signature = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $request->getContent(), $signature, $secret
            );
        } catch (\Throwable $e) {
            Log::error('Stripe webhook error: '.$e->getMessage());
            return response()->json(['error' => 'invalid'], 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $orderId = $session->metadata->order_id ?? null;

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
                break;

            case 'checkout.session.expired':
                $sessionId = $event->data->object->id ?? null;
                $order = Order::where('stripe_session_id', $sessionId)->first();
                if ($order && $order->status === 'pending') {
                    $order->status = 'failed';
                    $order->save();
                }
                break;
        }

        return response()->json(['received' => true]);
    }
}
