<?php

/**
 * API routes for the application. These routes will be prefixed with /api and will return JSON responses.
 * This is where you can define endpoints for fetching playlists, songs, user data, etc.
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
Route::get('/api/v1/playlists', [ApiController::class, 'getPlaylists']);
Route::post('/api/v1/cart/add', [ApiController::class, 'addToCart']);
Route::post('/api/v1/cart/remove', [ApiController::class, 'removeFromCart']);
Route::get('/api/v1/cart', [ApiController::class, 'getCart']);
*/
Route::get('/v1/banners', [ApiController::class, 'getBanners']);
Route::get('/v1/files', [ApiController::class, 'getFiles']);
Route::get('/v1/djs', [ApiController::class, 'getDjs']);
Route::get('/v1/legaltext', [ApiController::class, 'getLegalText']);
Route::get('/v1/metadata', [ApiController::class, 'getMetadata']);
Route::get('/v1/plans', [ApiController::class, 'getPlans']);
Route::post('/v1/login', [ApiController::class, 'login']);
Route::post('/v1/register', [ApiController::class, 'register']);
Route::post('/v1/recovery', [ApiController::class, 'recovery']);

Route::middleware('auth:sanctum')->get('/v1/user', [ApiController::class, 'getUser']);
Route::middleware('auth:sanctum')->get('/v1/orders', [ApiController::class, 'getOrders']);
Route::middleware('auth:sanctum')->post('/v1/logout', [ApiController::class, 'logout']);
    