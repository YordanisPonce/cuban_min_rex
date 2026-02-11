<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Download;
use App\Models\File;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Stripe\Subscription;

class FileController extends Controller
{
    public function download(Request $request, string $id)
    {
        $token = $request->get('token');
        Log::debug("Token de descarga: $token");
        if ($token) {
            Log::debug("Descarga con token");
            $user = User::whereJsonContains('downloadToken', $token)->first();
            if ($user) {
                Log::debug("Usuario encontrado para token de descarga: " . $user->id);
                $downloadToken = $user->downloadToken;
                $indice = array_search($token, $downloadToken);

               // unset($downloadToken[$indice]);
                $user->downloadToken = $downloadToken;
                $user->save();

                $file = File::find($id);
                $path = $file->original_file;
                if (!Storage::disk('s3')->exists($path)) {
                    abort(404);
                }
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $downloadName = "$file->name.$ext";
                return Storage::disk('s3')->download($path, $downloadName);
            }

            Log::debug("No entorn a ningun descargar");
            Log::debug("Usuario no encontrado para token de descarga: $token");
            return redirect('/')->with('error', 'Usted no tiene permisos para descargar el archivo seleccionado.');
        }

        if (auth()->user()->hasActivePlan()) {

            $plan = null;

            if (auth()->user()->currentPlan) {
                $plan = auth()->user()->currentPlan;
            } else {
                $plan = Order::where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->first()?->plan;
            }

            if($plan){
                if (auth()->user()->getFileDownloadsAtSubscriptionPeriod($id) < $plan->downloads) {
                    $file = File::find($id);

                    $path = $file->original_file;

                    if (!Storage::disk('s3')->exists($path)) {
                        abort(404);
                    }

                    $file->download_count = $file->download_count + 1;
                    $file->save();

                    $download = new Download();
                    $download->user_id = auth()->user()->id;
                    $download->file_id = $file->id;
                    $download->save();
                    $ext = pathinfo($path, PATHINFO_EXTENSION);
                    $downloadName = "$file->name.$ext";
                    return Storage::disk('s3')->download($path, $downloadName);
                }
                return redirect()->back()->with('error', 'Ha superados las descargas por mes permitida por su plan, considere mejorar su plan.');
            
            }
        }

        Log::debug("No entorn a ningun descargar");
        return redirect()->back()->with('error', 'Usted no tiene permisos para descargar el archivo seleccionado.');
    }

    public function play(string $collectionId, string $id)
    {
        $track = File::orderBy('created_at', 'desc')->get()
            ->map(fn($f) => [
                'id' => $f->id,
                'url' => Storage::disk('s3')->url($f->url ?? $f->file), // adapta si ya guardas rutas absolutas
                'title' => $f->title ?? $f->name,
            ]);

        return response()->json($track);
    }

    public function pay()
    {
        try {

            $cart = Cart::get_current_cart();

            $order = new Order();
            $order->user_id = auth()->user()?->id;
            $order->amount = $cart->get_cart_count();
            $order->status = 'pending';
            $order->save();

            $line_items = [];

            $files_url = [];

            foreach ($cart->items as $key => $value) {
                $file = File::find($value);
                if (!$file) {
                    return response()->json([
                        'error' => 'El archivo seleccionado no es válido.'
                    ], 422);
                }

                // Valida precio
                $price = (float) $file->price;
                if ($price <= 0) {
                    return response()->json([
                        'error' => 'El precio del archivo no es válido.'
                    ], 422);
                }
                    
                // Monto en centavos
                $amountInCents = (int) round($price * 100);

                $line_item = [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => (string) $file->name,
                        ],
                        'unit_amount' => $amountInCents,
                    ],
                    'quantity' => 1,
                ];

                array_push($line_items, $line_item);

                $order_item = new OrderItem();
                $order_item->order_id = $order->id;
                $order_item->file_id = $file->id;
                $order_item->save();

                // URL temporal al archivo
                $urlTemporal = Storage::disk('s3')->temporaryUrl($file->original_file, now()->addHour());

                array_push($files_url, (string) $urlTemporal);

            }

            // Configura tu clave secreta (recomendado: en AppServiceProvider::boot)
            Stripe::setApiKey(config('services.stripe.secret_key'));

            // Metadatos para rastrear compra
            $metadata = [
                'user_id' => auth()->check() ? (string) auth()->id() : null,
                'order_id' => (string) $order->id,
            ];

            // Crea la sesión de Checkout
            $session = StripeSession::create([
                'mode' => 'payment',
                'payment_method_types' => ['card'],
                'line_items' => $line_items,
                'success_url' => route('payment.ok2') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.ko'),

                // Si no manejas customers en Stripe, usa el email
                'customer_email' => optional(auth()->user())->email,

                // Metadatos en la Session (útil para búsqueda rápida)
                'metadata' => $metadata,

                // Metadatos en el PaymentIntent (bajan al cargo)
                'payment_intent_data' => [
                    'metadata' => $metadata,
                ],
            ]);

            return response()->json(['url' => $session->url]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            report($e);
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'error' => 'No se pudo iniciar el pago.',
            ], 500);
        }
    }

    public function payFile(string $id) { 

        try {

            $file = File::find($id);
            if (!$file) {
                return response()->json([
                    'error' => 'El archivo seleccionado no es válido.'
                ], 422);
            }

            // Valida precio
            $price = (float) $file->price;
            if ($price <= 0) {
                return response()->json([
                    'error' => 'El precio del archivo no es válido.'
                ], 422);
            }
                
            // Monto en centavos
            $amountInCents = (int) round($price * 100);

            $order = new Order();
            $order->user_id = auth()->user()?->id;
            $order->amount = $price;
            $order->status = 'pending';
            $order->save();
            
            $order_item = new OrderItem();
            $order_item->order_id = $order->id;
            $order_item->file_id = $file->id;
            $order_item->save();
            
            // Configura tu clave secreta (recomendado: en AppServiceProvider::boot)
            Stripe::setApiKey(config('services.stripe.secret_key'));

            // Metadatos para rastrear compra
            $metadata = [
                'user_id' => auth()->check() ? (string) auth()->id() : null,
                'order_id' => (string) $order->id,
            ];

            // Crea la sesión de Checkout
            $session = StripeSession::create([
                'mode' => 'payment',
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => (string) $file->name,
                            ],
                            'unit_amount' => $amountInCents,
                        ],
                        'quantity' => 1,
                    ]
                ],
                'success_url' => route('payment.ok2') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.ko'),

                // Si no manejas customers en Stripe, usa el email
                'customer_email' => optional(auth()->user())->email,

                // Metadatos en la Session (útil para búsqueda rápida)
                'metadata' => $metadata,

                // Metadatos en el PaymentIntent (bajan al cargo)
                'payment_intent_data' => [
                    'metadata' => $metadata,
                ],
            ]);

            return response()->json(['url' => $session->url]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            report($e);
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'error' => 'No se pudo iniciar el pago.',
            ], 500);
        }
    }

    public function addToCart(string $id){
        $user = Auth::user() ?? null;
        $cart = null;
        $items = [];
        if($user){
            $cart = $user->cart;
            if($cart){
                $items = $cart->items ?? [];
            } else {
                $cart = new Cart();
                $cart->user_id = $user->id;
                $cart->save();
            }
        } else {
            $unique_id = session()->get('unique_id');
            if ($unique_id) {
                $cart = Cart::where('uuid', $unique_id)->first();
                $items = $cart->items ?? [];
            } else {
                $uuid = Str::uuid();
                $cart = new Cart();
                $cart->uuid = $uuid;
                $cart->save();
                session()->put('unique_id', $uuid);
            }
        }
        array_push($items, $id);
        $cart->items = $items;
        $cart->save();
        return redirect()->back()->with('success','Archivo añadido al carrito.');
    }

    public function removeToCart(string $id){
        $user = Auth::user() ?? null;
        $cart = null;
        $items = [];
        if($user){
            $cart = $user->cart;
            if($cart){
                $items = $cart->items ?? [];
            } else {
                $cart = new Cart();
                $cart->user_id = $user->id;
                $cart->save();
            }
        } else {
            $unique_id = session()->get('unique_id');
            if ($unique_id) {
                $cart = Cart::where('uuid', $unique_id)->first();
                $items = $cart->items;
            } else {
                $uuid = Str::uuid();
                $cart = new Cart();
                $cart->uuid = $uuid;
                $cart->save();
                session()->put('unique_id', $uuid);
            }
        }
        if(in_array($id, $items)){
            $indice = array_search($id, $items);
            unset($items[$indice]);
            $cart->items = $items;
            $cart->save();
            return redirect()->back()->with('success','Archivo eliminado del carrito.');
        }
        return redirect()->back()->with('error','El archivo no está en su carrito.');
    }

    public function emptyCart(){
        $user = Auth::user() ?? null;
        $cart = null;
        $items = [];
        if($user){
            $cart = $user->cart;
            if($cart){
                $items = $cart->items;
            } else {
                $cart = new Cart();
                $cart->user_id = $user->id;
                $cart->save();
            }
        } else {
            $unique_id = session()->get('unique_id');
            if ($unique_id) {
                $cart = Cart::where('uuid', $unique_id)->first();
                $items = $cart->items;
            } else {
                $uuid = Str::uuid();
                $cart = new Cart();
                $cart->uuid = $uuid;
                $cart->save();
                session()->put('unique_id', $uuid);
            }
        }
        $cart->items = [];
        $cart->save();
        return redirect()->back()->with('success','Carrito vaciado.');
    }
}
