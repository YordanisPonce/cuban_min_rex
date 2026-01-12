<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\File;
use App\Models\Plan;
use App\Models\Sale;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\FilePaid;
use App\Services\StripeService;
use Filament\Livewire\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Stripe\Webhook;
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
                    $sale = new Sale();
                    $sale->user_id = $user?->id;
                    $sale->file_id = $value->file_id;
                    $sale->customer_email = $email;
                    $sale->amount = $value->file->price;
                    $sale->user_amount = $value->file->price * 0.7;
                    $sale->admin_amount = $value->file->price * 0.3;
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
                $cart->items = [];
                $cart->save();
            }
        }
    }

    public function handleInvoicePaid(array $payload)
    {
        $invoice = $payload['data']['object'];

        // SOLO renovaciones
        if (($invoice['billing_reason'] ?? null) !== 'subscription_cycle') {
            return;
        }

        // Metadata: viene en parent.subscription_details.metadata (en tu payload)
        $meta = $invoice['parent']['subscription_details']['metadata'] ?? [];

        // Fallback: también viene en lines.data[0].metadata
        if (empty($meta) && !empty($invoice['lines']['data'][0]['metadata'])) {
            $meta = $invoice['lines']['data'][0]['metadata'];
        }

        $userId = $meta['user_id'] ?? null;
        $planId = $meta['plan_id'] ?? null;

        // Fecha fin del periodo ya viene en el invoice
        $periodEndTs = $invoice['lines']['data'][0]['period']['end'] ?? null;
        if (!$periodEndTs) {
            Log::warning('invoice.paid renewal sin period.end', ['invoice_id' => $invoice['id'] ?? null]);
            return;
        }

        $user = $userId ? User::find($userId) : null;

        // Fallback por stripe_id (por si un día falta metadata)
        if (!$user && !empty($invoice['customer'])) {
            $user = User::where('stripe_id', $invoice['customer'])->first();
        }

        if (!$user) {
            Log::warning('invoice.paid renewal sin usuario', [
                'invoice_id' => $invoice['id'] ?? null,
                'customer' => $invoice['customer'] ?? null,
                'meta' => $meta,
            ]);
            return;
        }

        $periodEnd = Carbon::createFromTimestamp((int) $periodEndTs);

        // Actualiza tu user
        if ($planId) {
            $user->current_plan_id = (int) $planId;
        }
        $user->plan_expires_at = $periodEnd;
        $user->save();

        $lastOrder = Order::query()
            ->where('user_id', $user->id)
            ->where('status', 'paid')
            ->whereNotNull('plan_id')
            ->orderBy('created_at', 'DESC')
            ->first();

        $order = new Order([
            'user_id' => $user->id,
            'plan_id' => $planId,
            'status' => 'paid',
            'settled_at' => null,
            'amount' => $lastOrder->amount
        ]);

        $order->save();

        // Actualiza tu tabla Subscription custom
        Subscription::updateOrCreate(
            ['user_id' => $user->id],
            ['ends_at' => $periodEnd]
        );

        Log::info('Renovación OK (invoice.paid)', [
            'user_id' => $user->id,
            'invoice_id' => $invoice['id'] ?? null,
            'ends_at' => $periodEnd->toDateTimeString(),
        ]);
    }



}
