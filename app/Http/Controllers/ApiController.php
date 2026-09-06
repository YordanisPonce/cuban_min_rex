<?php

/**
 * Endpoint for API calls, such as fetching playlists, songs, etc.
 * This controller will handle all API requests and return JSON responses.
 */

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\PlayList;
use App\Models\File;
use App\Models\User;
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
     * Fetch a specific playlist by ID with its songs.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPlaylist($id)
    {
        $playlist = PlayList::with('items')->findOrFail($id);
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
        $files->transform(function ($file) {
            return [
                'title' => $file->name,
                'genre' => $file->isExclusive ? 'Exclusive': $file->categories()->first()?->name ?? 'Unknown',
                'artist' => $file->user?->name ?? 'Unknown',
                'audioUrl' => Storage::disk('s3')->url($file->file),
                'photoUrl' => $file->getPosterUrl() ?? $file->user->photo ?? config('app.logo_alter'),
                'publishedAt' => $file->created_at->toDateTimeString(),
                'downloads' => $file->download_count,
                'price' => $file->price,
            ];
        });
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
}