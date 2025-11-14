<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{

    public function showForm($planId)
    {
        $plans = Plan::all();
        return view('payment.payment', [
            'planId' => $planId,
            'plans' => $plans,
            'categories' => Category::where('show_in_landing', true)->get(),
            'djs' => User::where('role', 'worker')->orderBy('name')->get(),
            'recentCollections' => Collection::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
                return $item->files()->count() > 0;
            }),
            'recentCategories' => Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
                return $item->files()->count() > 0;
            })
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

            auth()->user()->createOrGetStripeCustomer();

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
                        'order_id' => $order->id,
                    ],
                    'subscription_data' => [
                        'metadata' => [
                            'plan_id' => $plan->id,
                            'user_id' => $request->user()->id,
                            'order_id' => $order->id,
                            // ... cualquier otro dato
                        ]
                    ]

                ]
            );

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

        $userId = auth()->user()->id;

        $dataToUpdate = [
            'canceled_at' => Carbon::now(),
        ];

        DB::transaction(function () use ($userId, $dataToUpdate) {
            Subscription::where('user_id', $userId)->whereNull('canceled_at')->update($dataToUpdate);
        });


        $categories = Category::where('show_in_landing', true)->get();
        $plans = Plan::orderBy('price')->get();
        $orders = Order::where('user_id', Auth::user()->id)->orderBy('paid_at', 'desc')->get();

        return redirect()->back()->with('success', 'Membresia cancelada satisfactoriamente');

    }

    public function showPaymentHistory(string $userId) {

        return view('filament.pages.user-payments', [
            "payments" => User::find($userId)->payments(),
        ]);

    }
}
