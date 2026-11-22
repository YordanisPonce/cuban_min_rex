<?php

use App\Filament\Pages\UserDownloadHistory;
use App\Filament\Pages\UserPayments;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImpersonationController;
use App\Http\Controllers\ManifestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\PaypalWebhookController;
use App\Http\Controllers\PlayListController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Middleware\IsUserMiddleware;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. Webhooks (fuera de IsUserMiddleware y sin CSRF)
|--------------------------------------------------------------------------
| Los llama Stripe/PayPal, no un usuario. Tambien conviene excluirlos en
| bootstrap/app.php con $middleware->validateCsrfTokens(except: [...]).
*/
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->name('stripe.webhook')
    ->withoutMiddleware([VerifyCsrfToken::class]);

Route::post('/paypal/webhook', [PaypalWebhookController::class, 'handle'])
    ->name('paypal.webhook')
    ->withoutMiddleware([VerifyCsrfToken::class]);

/*
|--------------------------------------------------------------------------
| 2. Utilidades
|--------------------------------------------------------------------------
*/
Route::get('/img/{filename}', [FileController::class, 'image'])
    ->where('filename', '[A-Za-z0-9._-]+');

// Salir de "impersonar" a un usuario (panel admin)
Route::middleware('auth')->post('/admin/impersonate/leave', [ImpersonationController::class, 'stop'])
    ->name('impersonate.leave');

/*
|--------------------------------------------------------------------------
| 3. Sitio web
|--------------------------------------------------------------------------
*/
Route::middleware(IsUserMiddleware::class)->group(function () {

    /*
    |----------------------------------------------------------------------
    | 3.1 Publicas (indexables)
    |----------------------------------------------------------------------
    */
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::controller(HomeController::class)->group(function () {
        Route::get('/plans', 'plan')->name('plans');
        Route::get('/djs', 'djs')->name('djs');
        Route::get('/djs/{dj}', 'dj')->name('dj');
        Route::get('/legal', 'legal')->name('legal');
        Route::get('/privacy', 'privacy')->name('privacy');
        Route::get('/cookies', 'cookies')->name('cookies');
        Route::get('/terms', 'terms')->name('terms');
        Route::get('/cart', 'cart')->name('cart');
    });

    /*
    |----------------------------------------------------------------------
    | 3.2 Publicas transaccionales (usar noindex en sus vistas)
    |----------------------------------------------------------------------
    */
    Route::get('/cart/pay', [FileController::class, 'pay'])->name('file.pay');
    Route::post('/paypal/process-cart', [PaypalController::class, 'processCart'])->name('paypal.process.cart');

    Route::get('/payment/cup/{file}', [PaymentController::class, 'showCUPForm'])->name('payment.cup.form');
    Route::post('/payment/cup/{file}/pay', [PaymentController::class, 'processCUPPayment'])->name('payment.cup.proccess');

    Route::get('/download/{file}/free', [FileController::class, 'getFree'])->name('file.free.download');
    Route::post('/download/{file}/free', [FileController::class, 'downloadFree'])->name('file.free.download.post');

    Route::view('/payment_ok', 'payment.ok', ['index' => 999])->name('payment.ok');
    Route::view('/payment_ok2', 'payment.ok2', ['index' => 999])->name('payment.ok2');
    Route::view('/payment_ko', 'payment.ko', ['index' => 999])->name('payment.ko');

    /*
    |----------------------------------------------------------------------
    | 3.3 Autenticacion
    |----------------------------------------------------------------------
    */
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout-user');

    Route::get('/auth/callback/google', [GoogleAuthController::class, 'callback'])->name('google.callback');

    /*
    |----------------------------------------------------------------------
    | 3.4 Area privada (requiere login y email verificado)
    |----------------------------------------------------------------------
    */
    Route::middleware(['auth', 'verified'])->group(function () {

        // Perfil
        Route::controller(ProfileController::class)
            ->prefix('profile')
            ->name('profile.')
            ->group(function () {
                Route::get('/', 'edit')->name('edit');
                Route::post('/', 'update')->name('update');
                Route::get('billing', 'billing')->name('billing');
                Route::post('billing', 'updateBilling')->name('updateBilling');
                Route::get('billing-link', 'getBillingLink')->name('billingLink');
                Route::get('restorePhoto', 'restorePhoto')->name('restorePhoto');
                Route::post('change-password', 'updatePassword')->name('changePassword');
                Route::get('delete', 'destroy')->name('destroy');
            });

        // Catalogo
        Route::get('/search', [SearchController::class, 'search'])->name('search');
        Route::get('/packs', [CollectionController::class, 'index'])->name('collection.index');

        Route::controller(HomeController::class)->group(function () {
            Route::get('/remixes', 'remixes')->name('remixes');
            Route::get('/mixes', 'mixes')->name('mixes');
            Route::get('/exclusives', 'exclusives')->name('exclusives');
            Route::get('/videos', 'videos')->name('videos');
            Route::get('/radio', 'radio')->name('radio');
            Route::get('/radio/remixes', 'radio_remixes')->name('radio.remixes');
        });

        // Archivos y carrito
        Route::controller(FileController::class)->group(function () {
            Route::get('/file/{file}', 'download')->name('file.download');
            Route::get('/info/file/{name}', 'info')->name('file.info');
            Route::get('/radio/file/{file}/pay', 'payFile')->name('radio.file.pay');
            Route::get('/files/cart/add/{file}', 'addToCart')->name('file.add.cart');
            Route::get('/files/cart/remove/{file}', 'removeToCart')->name('file.remove.cart');
            Route::get('/files/cart/empty', 'emptyCart')->name('file.empty.cart');
        });

        // Descarga firmada desde S3
        Route::get('/public/files/{path}', [FileController::class, 'publicDownload'])
            ->where('path', '.*')
            ->middleware('signed')
            ->name('public.files.download');

        // Pedidos
        Route::get('/order/{order}', [OrderController::class, 'details'])->name('order.download');
        Route::get('/order/{order}/download', [OrderController::class, 'download'])->name('order.download.all');

        // Pagos: Stripe
        Route::controller(PaymentController::class)->group(function () {
            Route::post('/payment/process', 'process')->name('payment.process');
            Route::get('/payment/cancel-subscription', 'cancelSubscription')->name('payment.cancelSubscription');
            Route::get('/plans/{plan}/payment', 'showForm')->name('payment.form');
            Route::get('/stripe/process/pack/{id}', 'processPack')->name('stripe.process.pack');
        });

        // Pagos: PayPal
        Route::controller(PaypalController::class)->prefix('paypal')->name('paypal.')->group(function () {
            Route::post('process', 'process')->name('process');
            Route::get('process/pack/{id}', 'processPack')->name('process.pack');
            Route::post('subscribe', 'subscribe')->name('subscribe');
            Route::get('return/{order}', 'returnPayPal')->name('return');
            Route::get('cancel/{order}', 'cancelPayPal')->name('cancel');
            Route::get('subscribe/return/{order}', 'returnSubscription')->name('subscribe.return');
            Route::get('subscribe/cancel/{order}', 'cancelSubscription')->name('subscribe.cancel');
        });

        // Playlists
        Route::controller(PlayListController::class)
            ->prefix('playlists')
            ->name('playlist.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('{playlist}', 'show')->name('show');
                Route::get('{playlist}/download', 'download')->name('download');
                Route::get('{playlist}/download/status/{uuid}', 'downloadStatus')->name('download.status');
                Route::get('{playlist}/download/status/{uuid}/check', 'downloadStatusCheck')->name('download.status.check');
                Route::get('{playlist}/download_item/{itemId}', 'download_item')->name('download_item');
                Route::get('{playlist}/add_to_cart', 'addToCart')->name('add.cart');
                Route::get('{playlist}/add_item_to_cart/{itemId}', 'addItemToCart')->name('add.item.cart');
                Route::get('{playlist}/remove_to_cart', 'removeToCart')->name('remove.cart');
                Route::get('{playlist}/remove_item_to_cart/{itemId}', 'removeItemToCart')->name('remove.item.cart');
                Route::get('lists/{playlist}/info/{name}', 'info')->name('item.info');
            });

        // JSON de pistas de una playlist (lo consume el reproductor)
        Route::get('/playlists-get-tracks/{id}', [PlayListController::class, 'tracks'])
            ->whereNumber('id')
            ->name('playlist.tracks');

        // DJs: seguir, notificar y puntuar
        Route::get('/djs/{dj}/follow', [FollowController::class, 'follow'])->name('follow');
        Route::get('/djs/{dj}/ntf', [FollowController::class, 'ntf'])->name('ntf');
        Route::post('/djs/{dj}/review', [ReviewController::class, 'rating_dj'])->name('rating.dj');

        // Reviews
        Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews');
        Route::post('/reviews/submit', [ReviewController::class, 'submit'])->name('submit.review');

        // Notificaciones
        Route::get('/notifications', [HomeController::class, 'ntfs'])->name('ntfs');
        Route::post('/notifications/update-settings', [HomeController::class, 'ntfs_setting_update'])->name('ntfs.update');
        Route::get('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('ntfs.read.all');
        Route::get('/notifications/delete-all', [NotificationController::class, 'deleteAll'])->name('ntfs.delete.all');
        Route::get('/notifications/delete/{id}', [NotificationController::class, 'delete'])->name('ntfs.delete');

        // Paginas de Filament expuestas como ruta
        // TODO: restringir a administradores
        Route::get('/admin/user-payments/{record}', UserPayments::class)->name('user.payments');
        Route::get('/admin/users/{record}/downloads', UserDownloadHistory::class)->name('user.downloads.history');
    });

    require __DIR__ . '/auth.php';
});

/*
|--------------------------------------------------------------------------
| 4. Manifiest
|--------------------------------------------------------------------------
*/
Route::get('/site.webmanifest', ManifestController::class)->name('manifest');