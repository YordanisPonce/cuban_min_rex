<?php

use App\Filament\Pages\DevDashboard;
use App\Filament\Pages\UserPayments;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlayListController;
use App\Http\Controllers\ReviewController;
use App\Http\Middleware\IsUserMiddleware;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\PlayList;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapGenerator;

Route::get('/sitemap', function () {
    SitemapGenerator::create(config('app.url'))->writeToFile(public_path('sitemap.xml'));
});

Route::middleware(IsUserMiddleware::class)->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login'); // mostrar login
    Route::post('/login', [AuthenticatedSessionController::class, 'store']); // procesar login
    Route::get('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout-user'); // logout

    Route::get('/auth/callback/google', function () {
        $googleUser = Socialite::driver('google')->user();
        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]
        );

        Auth::login($user);

        return redirect()->route('home');
    })->name('google.callback');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::get('/profile/billing', [ProfileController::class, 'billing'])->name('profile.billing');
        Route::post('/profile/billing', [ProfileController::class, 'updateBilling'])->name('profile.updateBilling');
        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/profile/restorePhoto', [ProfileController::class, 'restorePhoto'])->name('profile.restorePhoto');
        Route::post('/profile/change-password', [ProfileController::class, 'updatePassword'])->name('profile.changePassword');
        Route::get('/profile/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('/profile/billing-link', [ProfileController::class, 'getBillingLink'])->name('profile.billingLink');
    });

    // Crear orden y redirigir a Stripe
// Route::post('/checkout/{plan}', [CheckoutController::class, 'create'])
//     ->middleware('auth')
//     ->name('checkout.create');

    Route::post('/payment/process', [PaymentController::class, 'process'])
        ->middleware('auth')
        ->name('payment.process');
    Route::get('/payment/cancel-subscription', [PaymentController::class, 'cancelSubscription'])
        ->middleware('auth')
        ->name('payment.cancelSubscription');
    Route::get('plans/{plan}/payment', [PaymentController::class, 'showForm'])
        ->middleware('auth')
        ->name('payment.form');


    Route::view('/payment_ok', 'payment.ok')->name('payment.ok');
    Route::view('/payment_ok2', 'payment.ok2')->name('payment.ok2');
    Route::view('/payment_ko', 'payment.ko')->name('payment.ko');

    Route::get('/payment/cup/{file}', [PaymentController::class, 'showCUPForm'])->name('payment.cup.form');
    Route::post('/payment/cup/{file}/pay', [PaymentController::class, 'processCUPPayment'])->name('payment.cup.proccess');

    Route::get('/plans', [HomeController::class, 'plan'])->name('plans');
    Route::get('/djs', [HomeController::class, 'djs'])->name('djs');
    Route::get('/djs/{dj}', [HomeController::class, 'dj'])->name('dj');
    Route::get('/remixes', [HomeController::class, 'remixes'])->name('remixes');
    Route::get('/remixes/exclusives', [HomeController::class, 'exclusiveRemixes'])->name('remixes.exclusives');
    Route::get('/videos/exclusives', [HomeController::class, 'exclusiveVideos'])->name('videos.exclusives');
    Route::get('/videos', [HomeController::class, 'videos'])->name('videos');

    Route::get('/admin/user-payments/{record}', UserPayments::class)->name('user.payments');

    Route::get('/packs', [CollectionController::class, 'index'])->name('collection.index');

    Route::get('/file/{file}', [FileController::class, 'download'])
        ->name('file.download');

    Route::get('/order/{file}', [OrderController::class, 'download'])
        ->name('order.download');

    Route::get('/cart/pay', [FileController::class, 'pay'])
        ->name('file.pay');

    Route::get('/radio', [HomeController::class, 'radio'])->name('radio');
    Route::get('/radio/remixes', [HomeController::class, 'radio_remixes'])->name('radio.remixes');
    Route::get('/radio/file/{file}/pay', [FileController::class, 'payFile'])
        ->name('radio.file.pay');

    // Webhook de Stripe
    Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
        ->name('stripe.webhook');

    Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');

    Route::get('files/download/{path}', function (string $path) {
        if (!request()->hasValidSignature()) {
            abort(403); // URL expiró o es inválida
        }

        return Storage::disk('s3')->download($path);
    })->name('files.download');

    Route::get('files/cart/add/{file}', [FileController::class, 'addToCart'])
        ->name('file.add.cart');

    Route::get('files/cart/remove/{file}', [FileController::class, 'removeToCart'])
        ->name('file.remove.cart');
    
    Route::get('files/cart/empty', [FileController::class, 'emptyCart'])
        ->name('file.empty.cart');

    Route::get('cart', [HomeController::class, 'cart'])->name('cart');
    
    Route::get('public/files/{path}', function (string $path) {
        if (!request()->hasValidSignature()) {
            abort(403);
        }
        // Descargar o servir el archivo desde disco 'public'
        return Storage::disk('s3')->download($path);
    })->where('path', '.*')->name('public.files.download');


    Route::get('/playlists', [PlayListController::class, 'index'])->name('playlist.index');
    Route::get('/playlists/lists', [PlayListController::class, 'list'])->name('playlist.list');
    Route::get('/playlists/lists/{playlist}', [PlayListController::class, 'show'])->name('playlist.show');
    Route::get('/playlists/lists/{playlist}/download', [PlayListController::class, 'download'])->name('playlist.download');
    Route::get('/playlists/lists/{playlist}/download_item/{itemId}', [PlayListController::class, 'download_item'])->name('playlist.download_item');
    Route::get('/playlists/lists/{playlist}/add_to_cart', [PlayListController::class, 'addToCart'])->name('playlist.add.cart');
    Route::get('/playlists/lists/{playlist}/add_item_to_cart/{itemId}', [PlayListController::class, 'addItemToCart'])->name('playlist.add.item.cart');
    Route::get('/playlists/lists/{playlist}/remove_to_cart', [PlayListController::class, 'removeToCart'])->name('playlist.remove.cart');
    Route::get('/playlists/lists/{playlist}/remove_item_to_cart/{itemId}', [PlayListController::class, 'removeItemToCart'])->name('playlist.remove.item.cart');
    Route::get('/playlists-get-tracks/{id}', function($id){
        $playlist = PlayList::find($id);

        $tracks = $playlist->items()->get()->transform(function ($track) use($playlist) {
            return [
                'id' => (string) $track->id,
                'date' => $track->created_at,
                'artist' => $playlist->user->name,
                'title' => $track->title,
                'img' => $playlist->cover ? $playlist->getCoverUrl() : $playlist->user->photo ?? config('app.logo'),
                'bpm' => null,
                'playlist_id' => $playlist->id,
                'duration' => 120,
                'genre' => null,
                'badge' => null,
                'price' => $track->price,
                'url' => Storage::disk('s3')->url($track->file_path),
                'downloads' => $track->downloads->count(),
                'canDownload' => auth()->check() && auth()->user()->hasActivePlan(),
                'downloadLink' => auth()->check() && auth()->user()->hasActivePlan() ? route('playlist.download_item', [$playlist->name, $track->id]) : null,
                'addToCart' => route('playlist.add.item.cart', [$playlist->name, $track->id]),
            ];
        });

        if($playlist) return response()->json(['tracks' => $tracks]);

        return response(null, 404);
    });

    // Rutas para textos legales
    Route::get('/legal', [HomeController::class, 'legal'])->name('legal');
    Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
    Route::get('/cookies', [HomeController::class, 'cookies'])->name('cookies');
    Route::get('/terms', [HomeController::class, 'terms'])->name('terms');

    
    Route::get('/djs/{dj}/follow', [FollowController::class, 'follow'])
        ->middleware('auth')
        ->name('follow');
    Route::get('/djs/{dj}/ntf', [FollowController::class, 'ntf'])
        ->middleware('auth')
        ->name('ntf');
    Route::post('/djs/{dj}/review', [ReviewController::class, 'rating_dj'])
        ->middleware('auth')
        ->name('rating.dj');

    Route::get('/reviews', [ReviewController::class, 'index'])
        ->middleware('auth')
        ->name('reviews');

    Route::post('/reviews/submit', [ReviewController::class, 'submit'])
        ->middleware('auth')
        ->name('submit.review');

    Route::get('/notifications', [HomeController::class, 'ntfs'])
        ->middleware('auth')
        ->name('ntfs');

    Route::post('/notifications/update-settings', [HomeController::class, 'ntfs_setting_update'])
        ->middleware('auth')
        ->name('ntfs.update');

    Route::get('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->middleware('auth')
        ->name('ntfs.read.all');

    Route::get('/notifications/delete-all', [NotificationController::class, 'deleteAll'])
        ->middleware('auth')
        ->name('ntfs.delete.all');

    Route::get('/notifications/delete/{id}', [NotificationController::class, 'delete'])
        ->middleware('auth')
        ->name('ntfs.delete');

    require __DIR__ . '/auth.php';
});


