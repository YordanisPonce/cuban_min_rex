<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Sale;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\FilePaid;
use App\Services\PaypalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class PaypalWebhookController extends Controller
{
    public function __construct(private readonly PaypalService $paypalService)
    {
    }

    public function handle(Request $request)
    {
        $payload = $request->all();

        Log::info('PayPal webhook received', [
            'headers' => $request->headers->all(),
            'payload' => $payload,
            'ip' => $request->ip(),
        ]);

        $eventType = $payload['event_type'] ?? null;
        $resource = $payload['resource'] ?? [];

        if (!$eventType || !$resource) {
            Log::warning('PayPal webhook payload incomplete', [
                'event_type' => $eventType,
                'resource_keys' => array_keys($resource),
                'payload' => $payload,
            ]);

            return response()->json(['status' => 'ignored']);
        }

        Log::info('PayPal webhook event routed', [
            'event_type' => $eventType,
            'resource_id' => $resource['id'] ?? null,
            'payer_email' => $resource['payer']['email_address'] ?? null,
        ]);

        switch ($eventType) {
            case 'CHECKOUT.ORDER.APPROVED':
                $orderId = $this->resolveOrderIdFromWebhook($eventType, $resource);
                if (!$orderId) {
                    Log::warning('PayPal webhook order id missing', [
                        'event_type' => $eventType,
                        'resource' => $resource,
                    ]);

                    return response()->json(['status' => 'ignored']);
                }

                $order = Order::where('paypal_order_id', $orderId)->first();
                if (!$order) {
                    Log::warning('PayPal webhook order not found', [
                        'paypal_order_id' => $orderId,
                        'event_type' => $eventType,
                    ]);

                    return response()->json(['status' => 'ignored']);
                }

                if ($order->status === 'paid' && $order->paid_at) {
                    Log::info('PayPal webhook duplicate checkout approved event ignored', [
                        'order_id' => $order->id,
                        'paypal_order_id' => $orderId,
                        'event_type' => $eventType,
                        'paid_at' => $order->paid_at,
                    ]);

                    return response()->json(['status' => 'already_processed']);
                }

                Log::info('PayPal webhook order approved', [
                    'order_id' => $order->id,
                    'paypal_order_id' => $orderId,
                    'event_type' => $eventType,
                    'status' => $order->status,
                ]);

                try {
                    $capturePayload = $this->paypalService->captureOrder($orderId);

                    Log::info('PayPal webhook order capture executed', [
                        'order_id' => $order->id,
                        'paypal_order_id' => $orderId,
                        'capture_payload' => $capturePayload,
                    ]);
                } catch (\Throwable $th) {
                    Log::error('PayPal webhook approval capture failed', [
                        'order_id' => $order->id,
                        'paypal_order_id' => $orderId,
                        'message' => $th->getMessage(),
                    ]);

                    return response()->json(['status' => 'capture_failed']);
                }

                NotificationController::sendSistemNtf(
                    $order->user_id,
                    "Compra efectuada",
                    "Su compra esta siendo procesada. Recibirá una notificación cuando se haya verificado el pago.",
                );

                break;

            case 'PAYMENT.CAPTURE.COMPLETED':
                $orderId = $this->resolveOrderIdFromWebhook($eventType, $resource);
                if (!$orderId) {
                    Log::warning('PayPal webhook order id missing', [
                        'event_type' => $eventType,
                        'resource' => $resource,
                    ]);

                    return response()->json(['status' => 'ignored']);
                }

                $order = Order::where('paypal_order_id', $orderId)->first();
                if (!$order) {
                    Log::warning('PayPal webhook order not found', [
                        'paypal_order_id' => $orderId,
                        'event_type' => $eventType,
                    ]);

                    return response()->json(['status' => 'ignored']);
                }

                if ($order->status === 'paid' && $order->paid_at) {
                    Log::info('PayPal webhook duplicate checkout event ignored', [
                        'order_id' => $order->id,
                        'paypal_order_id' => $orderId,
                        'event_type' => $eventType,
                        'paid_at' => $order->paid_at,
                    ]);

                    return response()->json(['status' => 'already_processed']);
                }

                Log::info('PayPal webhook purchase branch', [
                    'order_id' => $order->id,
                    'paypal_order_id' => $orderId,
                    'event_type' => $eventType,
                ]);

                if($resource['status'] === 'COMPLETED'){

                    $order->status = 'paid';
                    $order->paid_at = Carbon::now();
                    $order->save();

                    $this->sendCustomerPurchaseNotification($order, $resource);
                    $this->registerOrderSales($order);

                    

                    NotificationController::sendBuyNtf(
                        $order->user_id,
                        "Pago Verificado",
                        "Su pago ha sido verificado y procesado correctamente. Puede descargar su archivo desde la bandeja de su correo.",
                    );
                
                } else {
                    Log::warning('PayPal webhook purchase not completed', [
                        'order_id' => $order->id,
                        'paypal_order_id' => $orderId,
                        'event_type' => $eventType,
                        'status' => $resource['status'],
                    ]);
                    if($resource['status'] === 'DENIED'){
                        $order->status = 'failed';
                        $order->save();

                        NotificationController::sendSistemNtf(
                            $order->user_id,
                            "Pago Denegado",
                            "Su pago ha sido denegado. Por favor, intente realizar la compra nuevamente.",
                        );
                    }
                }

                break;

            case 'PAYMENT.SALE.COMPLETED':
                $subscriptionId = $resource['billing_agreement_id'] ?? null;
                if (!$subscriptionId) {
                    Log::warning('PayPal webhook sale completed without billing agreement id', [
                        'event_type' => $eventType,
                        'resource' => $resource,
                    ]);

                    return response()->json(['status' => 'ignored']);
                }

                $order = Order::where('paypal_subscription_id', $subscriptionId)->first();
                if (!$order) {
                    Log::warning('PayPal webhook subscription order not found', [
                        'subscription_id' => $subscriptionId,
                        'event_type' => $eventType,
                    ]);

                    return response()->json(['status' => 'ignored']);
                }

                if ($order->status === 'paid' && $order->paid_at) {
                    Log::info('PayPal webhook duplicate payment sale completed event ignored', [
                        'order_id' => $order->id,
                        'subscription_id' => $subscriptionId,
                        'event_type' => $eventType,
                        'paid_at' => $order->paid_at,
                    ]);

                    return response()->json(['status' => 'already_processed']);
                }

                if($resource['state'] === 'completed') {
                    Log::info('PayPal webhook subscription payment confirmed', [
                        'order_id' => $order->id,
                        'subscription_id' => $subscriptionId,
                        'event_type' => $eventType,
                    ]);

                    $this->activatePlanFromWebhook($order, $resource);

                    NotificationController::sendBuyNtf(
                        $order->user_id,
                        "Pago de Suscripción Verificado",
                        "Su pago de suscripción ha sido verificado y procesado correctamente. Ahora puede disfrutar de los beneficios de su plan.",
                    );
                } else {
                    Log::warning('PayPal webhook subscription payment not completed', [
                        'order_id' => $order->id,
                        'subscription_id' => $subscriptionId,
                        'event_type' => $eventType,
                        'state' => $resource['state'],
                    ]);
                    if($resource['state'] === 'denied'){
                        $order->status = 'failed';
                        $order->save();

                        NotificationController::sendSistemNtf(
                            $order->user_id,
                            "Pago de Suscripción Denegado",
                            "Su pago de suscripción ha sido denegado. Por favor, intente realizar la compra nuevamente.",
                        );
                    }
                }

                break;

            case 'BILLING.SUBSCRIPTION.CREATED':
                $subscriptionId = $resource['id'] ?? null;
                $customId = $resource['custom_id'] ?? null;
                $status = $resource['status'] ?? null;

                if (!$subscriptionId) {
                    return response()->json(['status' => 'ignored']);
                }

                Log::info('PayPal webhook subscription created', [
                    'subscription_id' => $subscriptionId,
                    'event_type' => $eventType,
                    'status' => $status,
                ]);

                break;

            case 'BILLING.SUBSCRIPTION.ACTIVATED':
                $subscriptionId = $resource['id'] ?? null;
                $customId = $resource['custom_id'] ?? null;
                $status = $resource['status'] ?? null;

                if (!$subscriptionId) {
                    return response()->json(['status' => 'ignored']);
                }

                $order = $customId
                    ? Order::find($customId)
                    : Order::where('paypal_subscription_id', $subscriptionId)->first();

                if (!$order) {
                    Log::warning('PayPal webhook subscription approval order not found', [
                        'subscription_id' => $subscriptionId,
                        'custom_id' => $customId,
                        'event_type' => $eventType,
                    ]);

                    return response()->json(['status' => 'ignored']);
                }

                Log::info('PayPal webhook subscription approved by customer', [
                    'order_id' => $order->id,
                    'subscription_id' => $subscriptionId,
                    'event_type' => $eventType,
                    'status' => $status,
                ]);

                // Activar trial de 5 dias en lo que paypal confirma el pago
                $user = User::find($order->user_id);
                $plan = Plan::find($order->plan_id);
                if($user && $plan){
                    $user->current_plan_id = $plan->id;
                    $user->plan_start_at = Carbon::now();
                    $user->plan_expires_at = Carbon::now()->addDays(5);
                    $user->save();
                }

                NotificationController::sendSistemNtf(
                    $order->user_id,
                    "Suscripción Aprobada",
                    "Su suscripción ha sido aprobada y está siendo procesada. Recibirá una notificación cuando se haya verificado el pago.",
                );

                break;

            case 'BILLING.SUBSCRIPTION.CANCELLED':
                $subscriptionId = $resource['id'] ?? null;
                if (!$subscriptionId) {
                    return response()->json(['status' => 'ignored']);
                }

                $subscription = Subscription::where('stripe_id', $subscriptionId)->first();
                if ($subscription) {
                    $subscription->stripe_status = 'CANCELLED';
                    $subscription->ends_at = Carbon::now();
                    $subscription->save();
                }

                NotificationController::sendSistemNtf(
                    $subscription->user_id,
                    "Suscripción Cancelada",
                    "Su suscripción ha sido cancelada.",
                );

                break;

            default:
                Log::info('PayPal webhook event ignored', ['event_type' => $eventType]);
                break;
        }

        return response()->json(['status' => 'ok']);
    }

    protected function resolveOrderIdFromWebhook(string $eventType, array $resource): ?string
    {
        if ($eventType === 'PAYMENT.CAPTURE.COMPLETED') {
            $orderId = data_get($resource, 'supplementary_data.related_ids.order_id');
            if ($orderId) {
                return (string) $orderId;
            }

            $orderId = data_get($resource, 'purchase_units.0.reference_id');
            if ($orderId) {
                return (string) $orderId;
            }
        }

        if (isset($resource['id'])) {
            return (string) $resource['id'];
        }

        return data_get($resource, 'purchase_units.0.reference_id')
            ? (string) data_get($resource, 'purchase_units.0.reference_id')
            : null;
    }

    protected function activatePlanFromWebhook(Order $order, array $resource = []): void
    {
        $plan = $order->plan;
        $user = $order->user;

        if (!$plan || !$user) {
            Log::warning('PayPal webhook plan activation skipped due to missing relation', [
                'order_id' => $order->id,
                'plan_id' => $order->plan_id,
                'user_id' => $order->user_id,
            ]);

            return;
        }

        $order->status = 'paid';
        $order->paid_at = Carbon::now();
        $order->expires_at = Carbon::now()->addMonths($plan->duration_months);
        $order->save();

        //$user->current_plan_id = $plan->id;
        //$user->plan_start_at = Carbon::now();
        $user->plan_expires_at = Carbon::now()->addMonths($plan->duration_months);
        $user->save();

        Subscription::updateOrCreate(
            ['user_id' => $user->id],
            [
                'type' => 'paypal',
                'stripe_id' => $order->paypal_subscription_id ?? $order->paypal_order_id,
                'stripe_status' => 'ACTIVE',
                'ends_at' => Carbon::now()->addMonths($plan->duration_months),
            ]
        );

        Log::info('PayPal webhook plan activation completed', [
            'order_id' => $order->id,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);
    }

    protected function sendCustomerPurchaseNotification(Order $order, array $resource = []): void
    {
        $email = $resource['payer']['email_address'] ?? $order->user?->email ?? $order->customer_email;
        $order->customer_email = $email;
        $order->save();

        Log::info('PayPal webhook customer notification start', [
            'order_id' => $order->id,
            'customer_email' => $email,
            'user_id' => $order->user_id,
        ]);

        $user = User::where('id', $order->user_id)->orWhere('email', $email)->first();
        $token = Str::random(50);

        $user && $user->notify(new FilePaid(route('order.download', [$order->id, 'token' => $token])));

        if ($email && !$user) {
            $guestUser = User::where('email', 'user@guest.com')->first();
            $guestUser && Notification::route('mail', $email)->notify(new FilePaid(route('order.download', [$order->id, 'token' => $token])));
        }

        if ($user) {
            $downloadToken = $user->downloadToken ?? [];
            $downloadToken[] = $token;
            $user->downloadToken = $downloadToken;
            $user->save();
        }

        Log::info('PayPal webhook customer notification end', [
            'order_id' => $order->id,
            'token_generated' => filled($token),
            'user_found' => (bool) $user,
        ]);
    }

    protected function registerOrderSales(Order $order): void
    {
        $order->loadMissing('order_items.file', 'order_items.playlist', 'order_items.playlistItem');

        Log::info('PayPal webhook sales registration start', [
            'order_id' => $order->id,
            'item_count' => $order->order_items->count(),
        ]);

        foreach ($order->order_items as $value) {
            $price = $value->file ? $value->file->price : ($value->playlist ? $value->playlist->price : ($value->playlistItem ? $value->playlistItem->price : 0));

            Sale::create([
                'user_id' => $order->user_id,
                'file_id' => $value->file?->id,
                'play_list_id' => $value->playlist?->id,
                'play_list_item_id' => $value->playlistItem?->id,
                'amount' => $price,
                'user_amount' => $price * 0.7,
                'admin_amount' => $price * 0.1,
                'status' => 'pending',
                'customer_email' => $order->customer_email ?? $order->user?->email,
            ]);
        }

        $cart = User::find($order->user_id)?->cart;
        if ($cart) {
            $before_items = $cart->cart_items()->count();
            $cart->cart_items()->delete();
            Log::info('PayPal webhook cart reset', [
                'order_id' => $order->id,
                'items_before' => $before_items,
            ]);
        }

        Log::info('PayPal webhook sales registration end', [
            'order_id' => $order->id,
        ]);
    }
}
