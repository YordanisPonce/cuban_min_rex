<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\PaymentController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login'); // mostrar login
Route::post('/login', [AuthenticatedSessionController::class, 'store']); // procesar login
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout'); // logout

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
    Route::post('/profile/change-password', [ProfileController::class, 'updatePassword'])->name('profile.changePassword');
    Route::get('/profile/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');
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
Route::get('/{plan}/payment', [PaymentController::class, 'showForm'])
    ->middleware('auth')
    ->name('payment.form');


Route::view('/payment_ok', 'payment.ok')->name('payment.ok');
Route::view('/payment_ok2', 'payment.ok2')->name('payment.ok2');
Route::view('/payment_ko', 'payment.ko')->name('payment.ko');

Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/plans', [HomeController::class, 'plan'])->name('plans');

Route::get('/search', [SearchController::class, 'search'])->name('search');

Route::get('/categories/{category}/list', [CategoryController::class, 'show'])
    ->name('category.show');

Route::get('/categories/{category}/collections', [CategoryController::class, 'showCollections'])
    ->name('category.showCollections');

Route::get('/collections', [CollectionController::class, 'index'])
    ->name('collection.index');

Route::get('/collection/{collection}', [CollectionController::class, 'show'])
    ->name('collection.show');

Route::get('/collection/{collection}/play/{file}', [FileController::class, 'play'])
    ->name('file.play');

Route::get('/file/{file}', [FileController::class, 'download'])
    ->name('file.download');

Route::get('/file/{file}/payment', [FileController::class, 'pay'])
    ->middleware('auth')
    ->name('file.pay');

Route::get('/collection/download/{collection}', [CollectionController::class, 'download'])
    ->name('collection.download');

// Webhook de Stripe
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->name('stripe.webhook');

Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');

// routes/web.php
Route::get('/collections/{collection}/playlist', [\App\Http\Controllers\CollectionController::class, 'playlist'])
    ->name('collections.playlist');

Route::get('files/download/{path}', function (string $path) {
    if (!request()->hasValidSignature()) {
        abort(403); // URL expiró o es inválida
    }

    return Storage::disk('local')->download($path);
})->name('files.download');



Route::get('public/files/{path}', function (string $path) {
    if (!request()->hasValidSignature()) {
        abort(403);
    }
    // Descargar o servir el archivo desde disco 'public'
    return Storage::disk('public')->download($path);
})->where('path', '.*')->name('public.files.download');

require __DIR__ . '/auth.php';
