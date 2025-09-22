<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{

    public function showForm($planId)
    {
        $plans = Plan::all();
        return view('payment.payment', [
            'planId' => $planId,
            'plans' => $plans
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'plan_id' => 'required|integer'
        ]);

        // Buscar el plan en la BD
        $plan = Plan::find($request->plan_id);
        if (!$plan || !$plan->stripe_price_id) {
            return response()->json([
                'error' => 'El plan seleccionado no es válido o no tiene un precio en Stripe.'
            ], 422);
        }

        try {
            $session = auth()->user()->checkout(
                [$plan->stripe_price_id],
                [
                    'payment_method_types' => ['card'],
                    'line_items' => [
                        [
                            'price' => $plan->stripe_price_id,
                            'quantity' => 1,
                        ]
                    ],
                    'mode' => 'subscription',
                    'success_url' => route('payment.ok'),
                    'cancel_url' => route('payment.form', ['plan' => $plan->id]),
                    'metadata' => [
                        'plan_id' => $plan->id,
                        'user_id' => $request->user()->id,
                    ],
                ]
            );

            $order = new Order();
            $order->user_id = $request->user()->id;
            $order->plan_id = $plan->id;
            $order->amount = $plan->price;
            $order->status = 'pending';
            $order->save();

            $billing = $request->user()->billing;
            if (!$billing) {
                $billing = new Billing();
                $billing->user_id = $request->user()->id;
            }
            $billing->phone = $request->phone;
            $billing->address = $request->address;
            $billing->postal = $request->postal;
            $billing->country = $request->country;
            $billing->save();

            return response()->json(['url' => $session->url]);
        } catch (\Exception $e) {
            // ✅ Devolvemos JSON para que el front no rompa
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function cancelSubscription()
    {
        if (auth()->user()->subscribed('default')) {
            auth()->user()->subscription('default')->cancel();
        }

        auth()->user()->current_plan_id = null;
        auth()->user()->save();

        $categories = Category::where('show_in_landing', true)->get();
        $plans = Plan::orderBy('price')->get();
        $orders = Order::where('user_id', Auth::user()->id)->orderBy('paid_at', 'desc')->get();

        return redirect()->back()->with('success', 'Membresia cancelada satisfactoriamente');

    }
}
