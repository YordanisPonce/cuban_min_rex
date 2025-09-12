<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Models\Plan;

class PaymentController extends Controller
{

    public function showForm($planId)
    {
        $plans = Plan::all();
        return view('payment.payment', [
            'planId' => $planId,
            'plans'  => $plans
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

        \Stripe\Stripe::setApiKey(config('services.stripe.secret_key'));

        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'customer_email' => $request->email,
                'line_items' => [[
                    'price' => $plan->stripe_price_id, // ✅ Usamos el price_id correcto
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('payment.ok'),
                'cancel_url'  => route('payment.ko'),
            ]);

            return response()->json(['url' => $session->url]);
        } catch (\Exception $e) {
            // ✅ Devolvemos JSON para que el front no rompa
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
