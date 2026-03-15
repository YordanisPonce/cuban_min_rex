<?php

/**
 * API routes for the application. These routes will be prefixed with /api and will return JSON responses.
 * This is where you can define endpoints for fetching playlists, songs, user data, etc.
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::get('/api_v1/playlists', [ApiController::class, 'getPlaylists']);
Route::get('/api_v1/playlists/{id}', [ApiController::class, 'getPlaylist']);
Route::get('/api_v1/files', [ApiController::class, 'getFiles']);
Route::get('/api_v1/files/{id}', [ApiController::class, 'getFile']);
Route::post('/api_v1/cart/add', [ApiController::class, 'addToCart']);
Route::post('/api_v1/cart/remove', [ApiController::class, 'removeFromCart']);
Route::get('/api_v1/cart', [ApiController::class, 'getCart']);
Route::get('/api_v1/djs', [ApiController::class, 'getDjs']);
Route::get('/api_v1/djs/{id}', [ApiController::class, 'getDj']);
Route::post('/api_v1/authenticate', [ApiController::class, 'authenticate']);