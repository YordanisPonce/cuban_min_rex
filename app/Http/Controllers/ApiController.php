<?php

/**
 * Endpoint for API calls, such as fetching playlists, songs, etc.
 * This controller will handle all API requests and return JSON responses.
 */

namespace App\Http\Controllers;

use App\Models\AviablePaymentMethod;
use App\Models\Billing;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Download;
use App\Models\PlayList;
use App\Models\File;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Plan;
use App\Models\PlayListItem;
use App\Models\User;
use App\Services\PaypalService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class ApiController extends Controller
{
    /**
     * Fetch all songs.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFiles()
    {
        $files = File::all();
        $files->transform(function ($file) {
            return [
                'id' => $file->id,
                'title' => $file->name,
                'genre' => $file->isExclusive ? 'Exclusive': $file->categories()->first()?->name ?? 'Unknown',
                'artist' => $file->user?->name ?? 'Unknown',
                'bpm' => $file->bpm,
                'key' => $file->musical_note,
                'audioUrl' => Storage::disk('s3')->url($file->file),
                'photoUrl' => $file->getPosterUrl() ?? $file->user->photo ?? config('app.logo_alter'),
                'publishedAt' => $file->created_at->toDateTimeString(),
                'downloads' => $file->download_count,
                'price' => $file->price,
                'canBeDownloaded' => $file->canBeDownload()
            ];
        });
        return response()->json($files);
    }

    /**
     * Fetch all DJs (users with files).
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDJs()
    {
        $tops = User::join('files', 'users.id', '=', 'files.user_id')
            ->join('category_files', 'category_files.file_id', 'files.id')
            ->selectRaw('users.name, SUM(files.download_count) as downloads, users.photo' )
            ->groupBy(['users.name', 'users.photo'])
            ->orderBy('downloads', 'desc')
            ->get();
        $tops->transform(function ($dj) {
            return [
                'name' => $dj->name,
                'photoUrl' => $dj->photo ?? config('app.logo_alter'),
                'downloads' => $dj->downloads,
            ];
        });
        return response()->json($tops);
    }

    /**
     * Get the current Cart for the user with cart_items.
     */
    public function getCart()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'User Not Found'], 404);
        } 
        
        $cart = $user->cart;
        if(!$cart) {
            $cart = new Cart();
            $cart->user_id = $user->id;
            $cart->save();
        }

        $items = CartItem::where('cart_id', $cart->id)->whereNotNull('file_id')->get();
        if ($items) {
            $items->transform(function ($item) {
                return [
                    'id' => $item->file->id,
                    'title' => $item->file->name,
                    'genre' => $item->file->isExclusive ? 'Exclusive': $item->file->categories()->first()?->name ?? 'Unknown',
                    'artist' => $item->file->user?->name ?? 'Unknown',
                    'bpm' => $item->file->bpm,
                    'key' => $item->file->musical_note,
                    'audioUrl' => Storage::disk('s3')->url($item->file->file),
                    'photoUrl' => $item->file->getPosterUrl() ?? $item->file->user->photo ?? config('app.logo_alter'),
                    'publishedAt' => $item->file->created_at->toDateTimeString(),
                    'downloads' => $item->file->download_count,
                    'price' => $item->file->price,
                    'canBeDownloaded' => $item->file->canBeDownload()
                ];
            });
        }

        return response()->json($items ?? []);
    }

    /**
     * Add a file to the user's cart.
     */
    public function addToCart(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'User Not Found'], 404);
        } 
        
        $cart = $user->cart;
        if(!$cart) {
            $cart = new Cart();
            $cart->user_id = $user->id;
            $cart->save();
        }

        $fileId = intval(request()->input('file_id'));
        if (!$fileId) {
            return response()->json(['error' => 'Bad Request'], 400);
        } 

        $file = File::find($fileId);
        if(!$file){
            return response()->json(['error' => 'File Not Found'], 404);
        }

        CartItem::create([
            'cart_id' => $cart->id,
            'file_id' => $file->id,
            'amount' => $file?->price,
        ]);

        return response()->json(['success' => 'Item add to user cart']);
    }

    /**
     * Remove a file from the user's cart.
     */
    public function removeFromCart(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'User Not Found'], 404);
        } 
        
        $cart = $user->cart;
        if(!$cart) {
            $cart = new Cart();
            $cart->user_id = $user->id;
            $cart->save();
        }

        $fileId = intval(request()->input('file_id'));
        if (!$fileId) {
            return response()->json(['error' => 'Bad Request'], 400);
        } 

        $file = File::find($fileId);
        if(!$file){
            return response()->json(['error' => 'File Not Found'], 404);
        }

        CartItem::where('cart_id', $cart->id)->where('file_id', $file->id)->delete();

        return response()->json(['success' => 'Item add to user cart']);
    }

    /**
     * Clean cart
     */
    public function cleanCart(){
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'User Not Found'], 404);
        } 
        
        $cart = $user->cart;
        if(!$cart) {
            $cart = new Cart();
            $cart->user_id = $user->id;
            $cart->save();
        }

        $cart->cart_items()->delete();

        return response()->json(['success' => 'Carrito Limpio']);
    }

    /**
     * Proccess Cart payment
     */
    public function proccessCart(){
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'User Not Found'], 404);
        } 
        
        $cart = $user->cart;

        if (!$cart || $cart->cart_items()->count() === 0) {
            return response()->json([
                'error' => 'El carrito está vacío.',
            ], 422);
        }

        $payment_method = request()->input('payment_method');

        if (!$payment_method) {
            return response()->json([
                'error' => 'Bad Request',
            ], 400);
        }

        if ($payment_method === 'paypal') {
            $isPaypalAviable = AviablePaymentMethod::firstOrCreate([])->paypal;

            if(!$isPaypalAviable) {
                return response()->json(['error' => 'PayPal Payment Method is not aviable'],403);
            }

            $paypalService = new PaypalService();

            $order = Order::create([
                'user_id' => $user?->id,
                'amount' => $cart->get_cart_count(),
                'status' => 'pending',
                'currency' => 'USD',
            ]);

            foreach ($cart->cart_items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'file_id' => $item->file?->id,
                    'play_list_id' => $item->playlist?->id,
                    'play_list_item_id' => $item->playlistItem?->id,
                ]);
            }

            $checkout = $paypalService->createCartOrder(
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
        } else {
            try {

                $order = new Order();
                $order->user_id = $user->id;
                $order->amount = $cart->get_cart_count();
                $order->status = 'pending';
                $order->save();

                $line_items = [];

                $files_url = [];

                foreach ($cart->cart_items as $item) {
                    if ($item->file) {
                        $file = File::find($item->file->id);
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
                    if ($item->playlistItem) {
                        $file = PlayListItem::find($item->playlistItem->id);
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
                                    'name' => (string) 'Audio: '.$file->title,
                                ],
                                'unit_amount' => $amountInCents,
                            ],
                            'quantity' => 1,
                        ];

                        array_push($line_items, $line_item);

                        $order_item = new OrderItem();
                        $order_item->order_id = $order->id;
                        $order_item->play_list_item_id = $file->id;
                        $order_item->save();
                    }
                }

                // Configura tu clave secreta (recomendado: en AppServiceProvider::boot)
                Stripe::setApiKey(config('services.stripe.secret_key'));

                // Metadatos para rastrear compra
                $metadata = [
                    'user_id' => $user->id,
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
    }

    /**
     * Download a file
     */
    public function downloadFile(){
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'User Not Found'], 404);
        } 

        $fileId = intval(request()->input('file_id'));
        if (!$fileId) {
            return response()->json(['error' => 'Bad Request'], 400);
        } 

        $file = File::find($fileId);
        if(!$file){
            return response()->json(['error' => 'File Not Found'], 404);
        }
        
        if ($user->hasActivePlan() || $user->role === 'admin') {

            $plan = null;

            if ($user->currentPlan) {
                $plan = $user->currentPlan;
            } else {
                $plan = Order::where('user_id', $user->id)->where('status', 'paid')->whereNotNull('plan_id')->orderBy('created_at', 'desc')->first()?->plan;
            }

            if($plan || $user->role === 'admin'){
                if($user->plan_start_at || $user->role === 'admin'){
                    if ($user->role === 'admin' || $user->get_current_plan_consume_downloads() < $plan->downloads) {

                        $path = $file->original_file;

                        if (!Storage::disk('s3')->exists($path)) {
                            abort(404);
                        }

                        $file->download_count = $file->download_count + 1;
                        $file->save();

                        if($user->role !== 'admin'){
                            $download = new Download();
                            $download->user_id = $user->id;
                            $download->file_id = $file->id;
                            $download->amount = $user->downloads_cost();
                            $download->user_amount = $user->downloads_cost() * 0.7;
                            $download->admin_amount = $user->downloads_cost() * 0.1;
                            $download->save();
                        }

                        $ext = pathinfo($path, PATHINFO_EXTENSION);
                        $downloadName = "$file->name.$ext";
                        /*return Storage::disk('s3')->download($path, $downloadName);*/
                        return downloadFileFromDisk('s3', $path, $downloadName);
                    }
                }
                return response()->json(['error' => 'Ha superados las descargas por mes permitida por su plan, considere mejorar su plan.'], 401);            
            }
        }
        return response()->json(['error' => 'Usted no tiene permisos para descargar el archivo seleccionado.'], 403);
    }


    /**
     * Authenticate the user and return a token for API access.
     */
    public function login()
    {
        $credentials = [
            'email' => request()->input('email'),
            'password' => request()->input('password'),
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('cubanpoolApp')->plainTextToken;

            return response()->json([
                'token' => $token,
                'name' => $user->name,
                'email' => $user->email,
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function register(Request $request){

        $name = request()->input('email');
        $email = request()->input('email');
        $password = request()->input('password');

        if ($name && $email && $password) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'email_verified_at' => now(),
                'password' => Hash::make($password),
            ]);
            return response()->json(['success' => 'Usuario Registrado']);
        } else {
            return response()->json(['error' => 'Bad Request'], 400);
        }
    }

    public function recovery(){
        $email = request()->input('email');

        if (!$email) {
            return response()->json(['error' => 'Bad Request'], 400);
        }

        $status = Password::sendResetLink(['email' => $email]);

        if ($status == Password::RESET_LINK_SENT) {
            return response()->json(['success' => 'Reset link sent']);
        } else {
            return response()->json(['error' => 'Reset link not sent'], 401);
        }
    }

    public function password(){
        $currentPassword = request()->input('current_password');
        $newPassword = request()->input('new_password');
        $user = auth()->user();

        if ($currentPassword && $newPassword) {
            if (Hash::check($currentPassword, $user->password)) {
                $user->password = Hash::make($newPassword);
                $user->save();

                return response()->json(['success' => 'Contraseña actualizada']);
            } else {
                return response()->json(['error' => 'Contraseña actual erronea'], 401);
            }
        } else {
            return response()->json(['error' => 'Bad Request'], 400);
        }
    }

    /**
     * Get the user data for application
     */
    public function getUser(Request $request) {
        $transformPrice = "Sin plan activo";
        $dayLeft = 0;
        $downloadLeft = 0;

        if(auth()->user()->hasActivePlan()){
            $plan = auth()->user()->getActivePlan();
            $transformDuration = $plan?->duration_months > 1 ? "$plan?->duration_months meses" : 'mes';
            $transformPrice = "$ $plan->price / $transformDuration";

            $dayLeft = auth()->user()->planExpirationDays()->days;
            $downloadLeft = auth()->user()->get_current_plan_left_downloads();
        }

        $user = [
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
            'photo' => auth()->user()->photo ? Storage::disk('s3')->url(auth()->user()->photo) : asset('img/logo_alter.png'),
            'cover' => auth()->user()->cover ? Storage::disk('s3')->url(auth()->user()->cover) : asset('img/hero-base.jpeg'),
            'bio' => auth()->user()->bio ?? 'Sin definir',
            'role' => auth()->user()->role,
            'phone' => auth()->user()->billing ? auth()->user()->billing->phone : 'Sin definir',
            'address' => auth()->user()->billing ? auth()->user()->billing->address : 'Sin definir',
            'country' => auth()->user()->billing ? auth()->user()->billing->country : 'Sin definir',
            'postal_code' => auth()->user()->billing ? auth()->user()->billing->postal : 'Sin definir',
            'active_suscription' => auth()->user()->hasActivePlan(),
            'active_suscription_name' => auth()->user()->hasActivePlan() ? auth()->user()->getActivePlan()?->name : 'Sin plan activo',
            'active_suscription_price' => $transformPrice,
            'active_suscription_dayleft' => $dayLeft,
            'active_suscription_totaldays' => auth()->user()->hasActivePlan() ? auth()->user()->getActivePlan()?->duration_months * 30 : 0,
            'active_suscription_downloads_left' => $downloadLeft,
            'downloads_count' => auth()->user()->downloads()->count(),
            'sales_count' => auth()->user()->sales()->count(),
            'suscriptions_count' => auth()->user()->orders()->whereHas('plan')->where('status', 'paid')->count(),
        ];

        return response()->json($user);
    }

    public function update(){
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Not user'], 404);
        }

        $name = request()->input('name') ?? $user->name;
        $email = request()->input('email') ?? $user->email;

        $user->name = $name;
        $user->email = $email;
        $user->save();

        //modificar el billing
        $billing = $user->billing;
        if (!$billing) {
            $billing = new Billing();
            $billing->user_id = $user->id;
        }
        
        $phone = request()->input('phone') ?? $billing?->phone;
        $address = request()->input('address') ?? $billing?->address;
        $country = request()->input('country') ?? $billing?->country;
        $postal_code = request()->input('postal_code') ?? $billing?->postal;

        $billing->phone = $phone;
        $billing->address = $address;
        $billing->postal = $postal_code;
        $billing->country = $country;
        $billing->save();

        return response()->json(['success' => 'Información editada']);
    }

    /**
     * Get the users Orders for application
     */
    public function getOrders(Request $request){
        Carbon::setLocale('es');
        $orders = auth()->user()->orders()->orderBy('created_at', 'desc')->get();
        $orders->transform(function ($order) {
            return [
                'id' => $order->paypal_order_id ?? $order->stripe_payment_intent ?? "#ORD-$order->id",
                'title' => $order->plan_id ? 'Adquirir/Renovar Suscripción' : 'Compra de Artículos',
                'description' =>  $order->plan_id ? "Plan ".$order->plan->name : $order->order_items->count()." artículos",
                'created_at' => Carbon::parse($order->created_at)->translatedFormat('j \d\e F \d\e Y'),
                'amount' => $order->amount,
                'status' => $order->status === 'paid' ? 'completed' : $order->status,
            ];
        });
        return response()->json($orders);
    }

    /**
     * Logout the user by revoking the current access token.
     */
    public function logout(Request $request)
    {
        // Elimina el token actual del usuario autenticado
        auth()->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout exitoso']);
    }

    /**
     * Get all banners.
     */
    public function getBanners()
    {
        $banners = \App\Models\Banner::where('active', true)->get();

        if ($banners->count() === 0) {
            $banner = [
                'imageUrl' => asset('img/hero-base.jpeg'),
                'eyebrow' => '',
                'title' => '',
            ];
            return response()->json($banner);
        } else {
            $banners->transform(function ($banner) {
                return [
                    'imageUrl' => $banner->image(),
                    'eyebrow' => "",
                    'title' => "",
                ];
            });
        }

        return response()->json($banners);
    }

    /**
     * Get the legal text for the application.
     */
    public function getLegalText()
    {
        $legalText = \App\Models\LegalText::first();
        $legalText->transform(function ($text) {
            return [
                'terms' => $text->terms,
                'privacy' => $text->privacy,
                'cookies' => $text->cookies,
                'legal' => $text->legal,
            ];
        });
        return response()->json($legalText);
    }

    /**
     * Get the metadata for the application, including title, description, and keywords.
     */
    public function getMetadata()
    {
        $metadata = \App\Models\SeoText::first();
        return response()->json([
            'app_name' => $metadata->app_name,
            'app_description' => $metadata->app_description,
            'app_logo' => $metadata->logoUrl(),
            'contact_email' => $metadata->contact_email,
            'contact_phone' => $metadata->contact_phone,
            'contact_instagram' => $metadata->contact_instagram,
            'contact_youtube' => $metadata->contact_youtube,
            'contact_facebook' => $metadata->contact_facebook,
        ]);
    }

    /**
     * Get the Suscriptions Plans for the application
     */
    public function getPlans(){
        $plans = Plan::orderBy('price', 'asc')->get();
        $plans->transform(function ($plan) {
            return [
                'name' => $plan->name,
                'description' => $plan->description,
                'durationMonths' => $plan->duration_months,
                'price' => $plan->price,
                'features' => $plan->features,
                'downloads' => $plan->downloads,
            ];
        });
        return response()->json($plans);
    }
}