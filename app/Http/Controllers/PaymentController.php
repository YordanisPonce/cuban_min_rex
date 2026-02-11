<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Category;
use App\Models\File;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\CUPPaymentNotification;
use App\Services\ElToqueService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

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
            'recentDjs' => User::whereNot('role','user')->orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
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

        return redirect()->back()->with('success', 'Membresia cancelada satisfactoriamente');

    }

    public function showPaymentHistory(string $userId) {

        return view('filament.pages.user-payments', [
            "payments" => User::find($userId)->payments(),
        ]);

    }

    public function showCUPForm($fileId) {
        $file = File::find($fileId);
        if (!$file) {
            abort(404);
        }

        //$usdEnchange = ElToqueService::getUsdExchangeRate();

        $setting = Setting::first();

        return view('payment.cup_payment', [
            'file' => $file,
            'setting' => $setting,
            'categories' => Category::where('show_in_landing', true)->get(),
            'djs' => User::where('role', 'worker')->orderBy('name')->get(),
            'recentDjs' => User::whereNot('role','user')->orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
                return $item->files()->count() > 0;
            }),
            'recentCategories' => Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
                return $item->files()->count() > 0;
            })
        ]);
    }

    function processCUPPayment(Request $request, string $fileId) {

        try {
            $file = File::find($fileId);
            if (!$file) {
                abort(404);
            }

            $data = [
                'file_id' => $file->id,
                'amount' => $file->price * (Setting::first()->currency_convertion_rate),
                'status' => 'pending',
                'currency' => 'CUP',
                'phone' => $request->phone,
                'code' => $request->code,
                'customer_email' => $request->email,
            ];

            $order = new Order($data);
            $order->save();

            $order->order_items()->create([
                'file_id' => $file->id,
            ]);

            $setting = Setting::first();

            $notifyUser = User::where('email', $setting->confirmation_email)->first();

            if (!$notifyUser) {
                Notification::route('mail', $setting->confirmation_email)->notify(new CUPPaymentNotification($order));
            } else {
                $notifyUser->notify(new CUPPaymentNotification($order));
            }

            return view('payment.cup_payment_confirm', [
                'categories' => Category::where('show_in_landing', true)->get(),
                'djs' => User::where('role', 'worker')->orderBy('name')->get(),
                'recentDjs' => User::whereNot('role','user')->orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
                    return $item->files()->count() > 0;
                }),
                'recentCategories' => Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
                    return $item->files()->count() > 0;
                })
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
