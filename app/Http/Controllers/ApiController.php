<?php

/**
 * Endpoint for API calls, such as fetching playlists, songs, etc.
 * This controller will handle all API requests and return JSON responses.
 */

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Playlist;
use App\Models\File;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ApiController extends Controller
{
    /**
     * Fetch all playlists with their songs.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPlaylists()
    {
        $playlists = Playlist::with('items')->get();
        return response()->json($playlists);
    }

    /**
     * Fetch a specific playlist by ID with its songs.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPlaylist($id)
    {
        $playlist = Playlist::with('items')->findOrFail($id);
        return response()->json($playlist);
    }

    /**
     * Fetch all songs.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFiles()
    {
        $files = File::all();
        return response()->json($files);
    }

    /**
     * Fetch a specific song by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFile($id)
    {
        $file = File::findOrFail($id);
        return response()->json($file);
    }

    /**
     * Fetch all DJs (users with files).
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDJs()
    {
        $djs = User::whereHas('files')->get();
        return response()->json($djs);
    }

    /**
     * Fetch a specific DJ by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDJ($id)
    {
        $dj = User::whereHas('files')->findOrFail($id);
        return response()->json($dj);
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
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

}