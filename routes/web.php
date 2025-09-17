<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PaymentController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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

Route::middleware(['auth','verified'])->group(function (){
    // Route::get('/dashboard', function () {   //por ahora no lo utilizo
    //    return view('dashboard'); // resources/views/
    // })->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Crear orden y redirigir a Stripe
// Route::post('/checkout/{plan}', [CheckoutController::class, 'create'])
//     ->middleware('auth')
//     ->name('checkout.create');

Route::get('/payment/{plan}', [PaymentController::class, 'showForm'])
    ->middleware('auth')
    ->name('payment.form');

Route::post('/payment/process', [PaymentController::class, 'process'])
    ->middleware('auth')
    ->name('payment.process');

Route::view('/payment_ok', 'payment.ok')->name('payment.ok');
Route::view('/payment_ko', 'payment.ko')->name('payment.ko');

Route::view('/faq', 'faq')->name('faq');
Route::view('/contact', 'contact')->name('contact');

Route::get('/search', [SearchController::class, 'search'])->name('search');

// Webhook de Stripe
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook');

Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');

require __DIR__.'/auth.php';
