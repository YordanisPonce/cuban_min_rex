<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login'); // mostrar login
Route::post('/login', [AuthenticatedSessionController::class, 'store']); // procesar login
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout'); // logout

Route::middleware(['auth','verified'])->group(function (){
    // Route::get('/dashboard', function () {   //por ahora no lo utilizo
    //    return view('dashboard'); // resources/views/
    // })->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
