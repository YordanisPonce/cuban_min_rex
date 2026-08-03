<?php

namespace App\Http\Controllers;

use App\Models\AviablePaymentMethod;
use App\Models\Billing;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Plan;
use App\Models\Sale;
use App\Services\PaypalService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaypalController extends Controller
{
    public function __construct(private readonly PaypalService $paypalService)
    {
    }

    public function process(Request $request): JsonResponse
    {
        $isPaypalAviable = AviablePaymentMethod::firstOrCreate([])->paypal;

        if(!$isPaypalAviable) {
            abort(403);
        }
    
        $request->validate([
            'plan_id' => 'required|integer|exists:plans,id',
        ]);

        $plan = Plan::findOrFail($request->plan_id);
        $user = $request->user();

        $order = Order::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => $plan->price,
            'status' => 'pending',
            'currency' => 'USD',
        ]);

        $checkout = $this->paypalService->createOrderForPurchase(
            $plan,
            $order,
            route('paypal.return', ['order' => $order->id]),
            route('paypal.cancel', ['order' => $order->id])
        );

        $order->forceFill([
            'paypal_order_id' => $checkout['paypal_order_id'],
        ])->save();

        return response()->json([
            'order_id' => $order->id,
            'url' => $checkout['approval_url'],
            'status' => $checkout['status'],
        ]);
    }

    public function processCart(Request $request): JsonResponse
    {
        $isPaypalAviable = AviablePaymentMethod::firstOrCreate([])->paypal;

        if(!$isPaypalAviable) {
            abort(403);
        }
        
        $cart = Cart::get_current_cart();

        if (!$cart || $cart->cart_items()->count() === 0) {
            return response()->json([
                'error' => 'El carrito está vacío.',
            ], 422);
        }

        $user = $request->user();

        $order = Order::create([
            'user_id' => $user?->id,
            'amount' => $cart->get_cart_count(),
            'status' => 'pending',
            'currency' => 'USD',
        ]);

        foreach ($cart->cart_items as $item) {
            $payload = [
                'order_id' => $order->id,
            ];

            if ($item->file) {
                $payload['file_id'] = $item->file->id;
            }

            if ($item->playlist) {
                $payload['play_list_id'] = $item->playlist->id;
            }

            if ($item->playlistItem) {
                $payload['play_list_item_id'] = $item->playlistItem->id;
            }

            OrderItem::create($payload);
        }

        $checkout = $this->paypalService->createCartOrder(
            $order,
            route('paypal.return', ['order' => $order->id]),
            route('paypal.cancel', ['order' => $order->id])
        );

        $order->forceFill([
            'paypal_order_id' => $checkout['paypal_order_id'],
        ])->save();

        return response()->json([
            'order_id' => $order->id,
            'url' => $checkout['approval_url'],
            'status' => $checkout['status'],
        ]);
    }

    public function subscribe(Request $request): JsonResponse
    {
        $isPaypalAviable = AviablePaymentMethod::firstOrCreate([])->paypal;

        if(!$isPaypalAviable) {
            abort(403);
        }
    
        $request->validate([
            'email' => 'required|email',
            'plan_id' => 'required|integer|exists:plans,id',
            'phone' => 'required|string',
            'address' => 'required|string',
            'postal' => 'required|string',
            'country' => 'required|string',
        ]);

        $plan = Plan::findOrFail($request->plan_id);
        $user = $request->user();

        $billing = $user->billing;
        if (!$billing) {
            $billing = new Billing();
            $billing->user_id = $user->id;
        }

        $billing->phone = $request->phone;
        $billing->address = $request->address;
        $billing->postal = $request->postal;
        $billing->country = $request->country;
        $billing->save();

        $order = Order::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => $plan->price,
            'status' => 'pending',
            'currency' => 'USD',
        ]);

        $checkout = $this->paypalService->createSubscription(
            $plan,
            $user,
            $order,
            route('paypal.subscribe.return', ['order' => $order->id]),
            route('paypal.subscribe.cancel', ['order' => $order->id])
        );

        $order->forceFill([
            'paypal_subscription_id' => $checkout['subscription_id'],
        ])->save();

        return response()->json([
            'order_id' => $order->id,
            'subscription_id' => $checkout['subscription_id'],
            'url' => $checkout['approval_url'],
            'status' => $checkout['status'],
        ]);
    }

    public function returnPayPal(Order $order)
    {
        if (!$order->paypal_order_id) {
            Log::warning('PayPal return path reached without paypal order id', [
                'order_id' => $order->id,
            ]);

            return redirect()->route('payment.ko');
        }

        Log::info('PayPal return path reached, waiting for webhook confirmation', [
            'order_id' => $order->id,
            'paypal_order_id' => $order->paypal_order_id,
            'status' => $order->status,
        ]);

        return $order->plan_id
            ? redirect()->route('payment.ok')
            : redirect()->route('payment.ok2');
    }

    public function cancelPayPal(Order $order)
    {
        $order->status = 'failed';
        $order->save();

        return redirect()->route('payment.ko');
    }

    public function returnSubscription(Order $order)
    {
        if (!$order->paypal_subscription_id) {
            return redirect()->route('payment.ko');
        }

        return redirect()->route('payment.ok');
    }

    public function cancelSubscription(Order $order)
    {
        $order->status = 'failed';
        $order->save();

        return redirect()->route('payment.ko');
    }

    protected function registerOrderSales(Order $order): void
    {
        $order->loadMissing('order_items.file', 'order_items.playlist', 'order_items.playlistItem');

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
                'status' => 'paid',
                'customer_email' => $order->user?->email,
            ]);
        }

        $cart = Cart::get_current_cart();
        if ($cart) {
            $cart->cart_items()->delete();
        }
    }
}
