<?php

/**
 * Endpoint for API calls, such as fetching playlists, songs, etc.
 * This controller will handle all API requests and return JSON responses.
 */

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\PlayList;
use App\Models\File;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{
    /**
     * Fetch all playlists with their songs.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPlaylists()
    {
        $playlists = PlayList::with('items')->get();
        return response()->json($playlists);
    }

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
                //'extension' => $file->getExtension(),
                //'size' => $file->getSize(),
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
    public function getCart($id)
    {
        $cart = Cart::with('cart_items')->where('user_id', $id)->first();
        return response()->json($cart);
    }

    /**
     * Add a file to the user's cart.
     */
    public function addToCart(Request $request)
    {
        $fileId = $request->get('file_id');
        $file = File::findOrFail($fileId);
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        $cart->cart_items()->create(['file_id' => $file->id]);
        return response()->json(['message' => 'File added to cart']);
    }

    /**
     * Remove a file from the user's cart.
     */
    public function removeFromCart(Request $request)
    {
        $fileId = $request->get('file_id');
        $file = File::findOrFail($fileId);
        $cart = Cart::where('user_id', Auth::id())->first();
        $cart->cart_items()->where('file_id', $file->id)->delete();
        return response()->json(['message' => 'File removed from cart']);
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