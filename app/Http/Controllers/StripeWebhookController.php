<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Sale;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\FilePaid;
use App\Services\StripeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
use Illuminate\Support\Str;

class StripeWebhookController extends CashierController
{

    public function __construct(private readonly StripeService $stripeService)
    {
        parent::__construct();
    }

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
                $user->plan_start_at = Carbon::now();
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
        $pi = $payload['data']['object'];
        $session = $payload['data']['object'];
        $user_id = $session['metadata']['user_id'] ?? null;
        $orderId = $session['metadata']['order_id'] ?? null;
        $email = null;
        try {
            if (isset($pi['latest_charge'])) {
                $charge = $this->stripeService->getClient()->charges->retrieve($pi['latest_charge']);
                $email = $charge->receipt_email
                    ?? ($charge->billing_details->email ?? null);
            }

            if (!$email && !empty($pi['payment_method'])) {
                $pm = $this->stripeService->getClient()->paymentMethods->retrieve($pi['payment_method']);
                $email = $pm->billing_details->email ?? null;
            }
        } catch (\Throwable $th) {
            Log::error("Fallo en el intento de obtener el correo " . $th->getMessage());
        }
        Log::info('Payload', $payload);
        if ($orderId) {
            $user = User::where('id', $user_id)->orWhere('email', $email)->first();
            $order = Order::find($orderId);
            if ($order) {
                $order->status = 'paid';
                $order->paid_at = Carbon::now();
                $order->customer_email = $email;
                $order->save();

                foreach ($order->order_items as $key => $value) {
                    $price = $value->file ? $value->file->price : ($value->playlist ? $value->playlist->price : ($value->playlistItem ? $value->playlistItem->price : 0));

                    $sale = new Sale();
                    $sale->user_id = $user?->id;
                    $sale->file_id = $value->file ? $value->file->id : null;
                    $sale->play_list_id = $value->playlist ? $value->playlist->id : null;
                    $sale->play_list_item_id = $value->playlistItem ? $value->playlistItem->id : null;
                    $sale->amount = $price;
                    $sale->user_amount = $price * 0.7;
                    $sale->admin_amount = $price * 0.1;
                    $sale->customer_email = $email;
                    $sale->save();
                }

                $token = Str::random(50);

                //Aqui configurar para enviar el correo al cliente
                $user && $user->notify(new FilePaid(route('order.download', [$order->id, 'token' => $token])));
                if ($email && !$user) {
                    $user = User::where('email', 'user@guest.com')->first();
                    Notification::route('mail', $email)->notify(new FilePaid(route('order.download', [$order->id, 'token' => $token])));
                }

                $downloadToken = $user?->downloadToken ?? [];
                array_push($downloadToken, $token);
                $user->downloadToken = $downloadToken;
                $user->save();

                $cart = Cart::get_current_cart();
                $cart->cart_items()->delete();
            }
        }
    }

    public function handleInvoicePaid(array $payload)
    {
        $invoice = $payload['data']['object'];

        if (($invoice['billing_reason'] ?? null) === 'subscription_cycle') {
            $userId = $invoice['metadata']['user_id'] ?? null;
            $planId = $invoice['metadata']['plan_id'] ?? null;

            // fallback: metadata en el price
            if (!$planId && !empty($invoice['lines']['data'][0]['price']['metadata']['plan_id'])) {
                $planId = $invoice['lines']['data'][0]['price']['metadata']['plan_id'];
            }

            $periodEndTs = $invoice['lines']['data'][0]['period']['end'] ?? null;
            if (!$periodEndTs) {
                Log::warning('invoice.paid sin period.end', ['invoice_id' => $invoice['id']]);
                return;
            }

            $user = $userId ? User::find($userId) : User::where('stripe_id', $invoice['customer'])->first();
            if (!$user) {
                Log::warning('invoice.paid sin usuario', ['invoice_id' => $invoice['id']]);
                return;
            }

            $periodEnd = Carbon::createFromTimestamp($periodEndTs);
            $plan = Plan::find($planId);

            // Actualiza usuario
            if ($planId) {
                $user->current_plan_id = $planId;
            }
            $user->plan_start_at = Carbon::now();
            $user->plan_expires_at = $periodEnd;
            $user->save();

            $order = new Order();
            $order->user_id = $user->id;
            $order->plan_id = $planId;
            $order->amount = $plan?->price;
            $order->status = 'paid';
            $order->paid_at = Carbon::now();
            $order->customer_email = $user->email;
            $order->save();

            // Actualiza o crea Subscription
            Subscription::updateOrCreate(
                ['user_id' => $user->id],
                ['ends_at' => $periodEnd]
            );

            Log::info('Renovación OK', [
                'user_id' => $user->id,
                'invoice_id' => $invoice['id'],
                'ends_at' => $periodEnd->toDateTimeString(),
            ]);
        }

        // Solo si es la primera factura de la suscripción 
        if (($invoice['billing_reason'] ?? null) == 'subscription_create') {

            $orderId = $invoice['metadata']['order_id'] ?? null;
            $userId = $invoice['metadata']['user_id'] ?? null;
            $planId = $invoice['metadata']['plan_id'] ?? null;

            if ($orderId && $userId && $planId) {
                $order = Order::find($orderId);
                $user = User::find($userId);
                $plan = Plan::find($planId);
                if ($order && $user && $plan) {

                    $order->status = 'paid';
                    $order->paid_at = now();
                    $order->expires_at = now()->addMonths($plan->duration_months);
                    $order->save();

                    $user->current_plan_id = $plan->id;
                    $user->plan_start_at = Carbon::now();
                    $user->plan_expires_at = now()->addMonths($plan->duration_months);
                    $user->save();

                    $subscription = new Subscription();
                    $subscription->user_id = $user->id;
                    $subscription->ends_at = now()->addMonths($plan->duration_months);
                    $subscription->save();
                }
            }
        }
    }
}
