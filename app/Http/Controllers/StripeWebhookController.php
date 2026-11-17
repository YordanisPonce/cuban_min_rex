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
use Illuminate\Support\Facades\DB;

/*
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

                Subscription::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'plan_name' => $plan->name,
                        'plan_price' => $plan->price,
                        'max_downloads' => $plan->downloads,
                        'ends_at' => now()->addMonths($plan->duration_months)
                    ]
                );
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

                if ($order->downloadPack) {
                    $user = $order->user;
                    if ($user) {
                        $susc = $user->getActiveSuscription();
                        $susc->update([
                            'extra_downloads' => $susc->extra_downloads + $order->downloadPack->extra_downloads
                        ]);
                    }
                } else {

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
                ['plan_name' => $plan->name, 'plan_price' => $plan->price, 'max_downloads' => $plan->downloads, 'ends_at' => $periodEnd]
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

                    Subscription::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'plan_name' => $plan->name,
                            'plan_price' => $plan->price,
                            'max_downloads' => $plan->downloads,
                            'ends_at' => now()->addMonths($plan->duration_months)
                        ]
                    );
                }
            }
        }
    }

    public function handleCustomerSubscriptionDeleted(array $payload)
    {

        $session = $payload['data']['object'];
        $userId = $session['metadata']['user_id'] ?? null;
        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $user->update([
                    'current_plan_id' => null,
                ]);
            }
        }
    }
}
*/

class StripeWebhookController extends CashierController
{
    public function __construct(private readonly StripeService $stripeService)
    {
        parent::__construct();
    }

    /* =====================================================================
     |  Helpers
     | ===================================================================== */

    /**
     * La metadata de la suscripción se copia a la factura en
     * parent.subscription_details.metadata (API nueva) o en
     * subscription_details.metadata (API antigua). NO está en $invoice['metadata'].
     */
    private function invoiceSubscriptionMetadata(array $invoice): array
    {
        $legacy = $invoice['subscription_details']['metadata'] ?? [];
        $current = $invoice['parent']['subscription_details']['metadata'] ?? [];

        return array_merge((array) $legacy, (array) $current);
    }

    private function invoiceStripeSubscriptionId(array $invoice): ?string
    {
        return $invoice['parent']['subscription_details']['subscription']
            ?? $invoice['subscription']
            ?? null;
    }

    /** Fin real del periodo facturado, según Stripe. */
    private function invoicePeriodEnd(array $invoice): ?Carbon
    {
        $timestamp = collect($invoice['lines']['data'] ?? [])
            ->pluck('period.end')
            ->filter()
            ->max();

        return $timestamp ? Carbon::createFromTimestamp($timestamp) : null;
    }

    private function invoicePriceId(array $invoice): ?string
    {
        $line = $invoice['lines']['data'][0] ?? [];

        return $line['pricing']['price_details']['price']   // API nueva
            ?? $line['price']['id']                          // API antigua
            ?? null;
    }

    private function resolvePlan(?string $planId, ?string $priceId): ?Plan
    {
        $plan = $planId ? Plan::find($planId) : null;

        if (!$plan && $priceId) {
            $plan = Plan::where('stripe_price_id', $priceId)->first();
        }

        return $plan;
    }

    private function resolveUser(?string $userId, ?string $stripeCustomerId): ?User
    {
        $user = $userId ? User::find($userId) : null;

        if (!$user && $stripeCustomerId) {
            $user = User::where('stripe_id', $stripeCustomerId)->first();
        }

        return $user;
    }

    /** Asigna el plan al usuario y crea/actualiza su Subscription. */
    private function activatePlan(User $user, Plan $plan, Carbon $periodEnd, ?string $stripeSubscriptionId, ?string $priceId): void
    {
        $user->current_plan_id = $plan->id;
        $user->plan_start_at = Carbon::now();
        $user->plan_expires_at = $periodEnd;
        $user->save();

        $values = [
            'type' => 'default',
            'stripe_status' => 'active',
            'stripe_price' => $priceId ?? $plan->stripe_price_id,
            'quantity' => 1,
            'plan_name' => $plan->name,
            'plan_price' => $plan->price,
            'max_downloads' => $plan->downloads,
            'ends_at' => $periodEnd,
            'canceled_at' => null,
        ];

        if ($stripeSubscriptionId) {
            $values['stripe_id'] = $stripeSubscriptionId;
        }

        Subscription::updateOrCreate(['user_id' => $user->id], $values);
    }

    /* =====================================================================
     |  customer.subscription.created
     | ===================================================================== */

    /**
     * Solo registra el evento. La activación del plan se hace en invoice.paid,
     * cuando el pago está confirmado (aquí la suscripción puede estar "incomplete").
     */
    public function handleCustomerSubscriptionCreated(array $payload)
    {
        $subscription = $payload['data']['object'];

        Log::info('Stripe: suscripción creada (se activa en invoice.paid)', [
            'subscription_id' => $subscription['id'] ?? null,
            'status' => $subscription['status'] ?? null,
            'user_id' => $subscription['metadata']['user_id'] ?? null,
            'plan_id' => $subscription['metadata']['plan_id'] ?? null,
        ]);
    }

    /* =====================================================================
     |  payment_intent.succeeded  (compras de archivos / paquetes de descargas)
     | ===================================================================== */

    public function handlePaymentIntentSucceeded(array $payload)
    {
        $pi = $payload['data']['object'];
        $userId = $pi['metadata']['user_id'] ?? null;
        $orderId = $pi['metadata']['order_id'] ?? null;
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

        // Los pagos de suscripción también disparan este evento, pero sin order_id.
        if (!$orderId) {
            return;
        }

        $order = Order::find($orderId);
        if (!$order) {
            Log::warning('payment_intent.succeeded: orden no encontrada', ['order_id' => $orderId]);
            return;
        }

        $user = $userId ? User::find($userId) : null;
        if (!$user && $email) {
            $user = User::where('email', $email)->first();
        }

        $order->status = 'paid';
        $order->paid_at = Carbon::now();
        $order->customer_email = $email;
        $order->save();

        // Paquete de descargas extra
        if ($order->downloadPack) {
            $susc = $order->user?->getActiveSuscription();

            if (!$susc) {
                Log::warning('payment_intent.succeeded: sin suscripción activa para sumar descargas', [
                    'order_id' => $order->id,
                ]);
                return;
            }

            $susc->update([
                'extra_downloads' => $susc->extra_downloads + $order->downloadPack->extra_downloads
            ]);
            return;
        }

        // Compra de archivos / playlists
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
        $downloadUrl = route('order.download', [$order->id, 'token' => $token]);

        // Aqui configurar para enviar el correo al cliente
        $user && $user->notify(new FilePaid($downloadUrl));

        // Usuario que guardará el token de descarga (el invitado usa el usuario "guest")
        $tokenOwner = $user;
        if ($email && !$user) {
            $tokenOwner = User::where('email', 'user@guest.com')->first();
            Notification::route('mail', $email)->notify(new FilePaid($downloadUrl));
        }

        if ($tokenOwner) {
            $downloadToken = $tokenOwner->downloadToken ?? [];
            $downloadToken[] = $token;
            $tokenOwner->downloadToken = $downloadToken;
            $tokenOwner->save();
        } else {
            Log::warning('payment_intent.succeeded: no hay usuario para guardar el token de descarga', [
                'order_id' => $order->id,
            ]);
        }

        $cart = Cart::get_current_cart();
        $cart?->cart_items()->delete();
    }

    /* =====================================================================
     |  invoice.paid  (único punto de activación y renovación de planes)
     | ===================================================================== */

    public function handleInvoicePaid(array $payload)
    {
        $invoice = $payload['data']['object'];
        $billingReason = $invoice['billing_reason'] ?? null;

        if (!in_array($billingReason, ['subscription_create', 'subscription_cycle'], true)) {
            return;
        }

        if (($invoice['status'] ?? 'paid') !== 'paid') {
            Log::warning('invoice.paid con estado distinto de paid', [
                'invoice_id' => $invoice['id'] ?? null,
                'status' => $invoice['status'] ?? null,
            ]);
            return;
        }

        $metadata = $this->invoiceSubscriptionMetadata($invoice);
        $priceId = $this->invoicePriceId($invoice);
        $stripeSubscriptionId = $this->invoiceStripeSubscriptionId($invoice);
        $periodEnd = $this->invoicePeriodEnd($invoice);

        if (!$periodEnd) {
            Log::warning('invoice.paid sin period.end', ['invoice_id' => $invoice['id'] ?? null]);
            return;
        }

        $user = $this->resolveUser($metadata['user_id'] ?? null, $invoice['customer'] ?? null);
        if (!$user) {
            Log::warning('invoice.paid sin usuario', ['invoice_id' => $invoice['id'] ?? null]);
            return;
        }

        $plan = $this->resolvePlan($metadata['plan_id'] ?? null, $priceId);
        if (!$plan) {
            Log::warning('invoice.paid sin plan', [
                'invoice_id' => $invoice['id'] ?? null,
                'price_id' => $priceId,
            ]);
            return;
        }

        if ($billingReason === 'subscription_create') {
            $this->processFirstInvoice($invoice, $metadata, $user, $plan, $periodEnd, $stripeSubscriptionId, $priceId);
            return;
        }

        $this->processRenewal($invoice, $user, $plan, $periodEnd, $stripeSubscriptionId, $priceId);
    }

    /** Primera factura de la suscripción: marca la orden como pagada y activa el plan. */
    private function processFirstInvoice(array $invoice, array $metadata, User $user, Plan $plan, Carbon $periodEnd, ?string $stripeSubscriptionId, ?string $priceId): void
    {
        DB::transaction(function () use ($invoice, $metadata, $user, $plan, $periodEnd, $stripeSubscriptionId, $priceId) {
            $orderId = $metadata['order_id'] ?? null;
            $order = $orderId ? Order::lockForUpdate()->find($orderId) : null;

            // Idempotencia: Stripe puede reenviar el mismo evento
            if ($order && $order->status === 'paid') {
                Log::info('invoice.paid ya procesada', [
                    'invoice_id' => $invoice['id'] ?? null,
                    'order_id' => $order->id,
                ]);
                return;
            }

            if ($order) {
                $order->status = 'paid';
                $order->paid_at = Carbon::now();
                $order->expires_at = $periodEnd;
                $order->save();
            } else {
                Log::warning('invoice.paid (create) sin orden asociada; se activa el plan igualmente', [
                    'invoice_id' => $invoice['id'] ?? null,
                ]);
            }

            $this->activatePlan($user, $plan, $periodEnd, $stripeSubscriptionId, $priceId);
        });
    }

    /** Renovación mensual/anual: crea una nueva orden y extiende el plan. */
    private function processRenewal(array $invoice, User $user, Plan $plan, Carbon $periodEnd, ?string $stripeSubscriptionId, ?string $priceId): void
    {
        DB::transaction(function () use ($invoice, $user, $plan, $periodEnd, $stripeSubscriptionId, $priceId) {
            $existing = Subscription::where('user_id', $user->id)->lockForUpdate()->first();

            // Idempotencia: si este periodo ya está registrado, no se duplica la orden
            if ($existing && $existing->ends_at && Carbon::parse($existing->ends_at)->gte($periodEnd)) {
                Log::info('Renovación ya procesada', [
                    'user_id' => $user->id,
                    'invoice_id' => $invoice['id'] ?? null,
                ]);
                return;
            }

            $order = new Order();
            $order->user_id = $user->id;
            $order->plan_id = $plan->id;
            $order->amount = $plan->price;
            $order->status = 'paid';
            $order->paid_at = Carbon::now();
            $order->expires_at = $periodEnd;
            $order->customer_email = $user->email;
            $order->save();

            $this->activatePlan($user, $plan, $periodEnd, $stripeSubscriptionId, $priceId);

            Log::info('Renovación OK', [
                'user_id' => $user->id,
                'invoice_id' => $invoice['id'] ?? null,
                'ends_at' => $periodEnd->toDateTimeString(),
            ]);
        });
    }

    /* =====================================================================
     |  customer.subscription.deleted
     | ===================================================================== */

    public function handleCustomerSubscriptionDeleted(array $payload)
    {
        $data = $payload['data']['object'];
        $stripeSubscriptionId = $data['id'] ?? null;

        $user = $this->resolveUser($data['metadata']['user_id'] ?? null, $data['customer'] ?? null);
        if (!$user) {
            Log::warning('subscription.deleted sin usuario', ['subscription_id' => $stripeSubscriptionId]);
            return;
        }

        $subscription = Subscription::where('user_id', $user->id)->first();

        // Si el usuario ya tiene otra suscripción vigente (cambio de plan), no se toca
        if ($subscription && $subscription->stripe_id && $stripeSubscriptionId && $subscription->stripe_id !== $stripeSubscriptionId) {
            Log::info('subscription.deleted ignorada: no es la suscripción vigente', [
                'user_id' => $user->id,
                'deleted' => $stripeSubscriptionId,
                'current' => $subscription->stripe_id,
            ]);
            return;
        }

        $endedAt = !empty($data['ended_at'])
            ? Carbon::createFromTimestamp($data['ended_at'])
            : Carbon::now();

        DB::transaction(function () use ($user, $subscription, $endedAt) {
            $user->update(['current_plan_id' => null]);

            $subscription?->update([
                'stripe_status' => 'canceled',
                'ends_at' => $endedAt,
                'candeled_at' => $endedAt,
            ]);
        });
    }
}