<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Download;
use App\Models\PlayList;
use App\Models\User;
use App\Models\Cart;
use App\Models\PlayListItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class PlayListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // NavBar Data
        $categories = Category::where('show_in_landing', true)->orderBy('name')->get();
        $djs = User::whereHas('files')->orderBy('name')->get();

        $playlists = PlayList::orderBy('created_at', 'desc')->paginate(30);

        return view('playlists', compact('djs', 'categories', 'playlists'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // NavBar Data
        $categories = Category::where('show_in_landing', true)->orderBy('name')->get();
        $djs = User::whereHas('files')->orderBy('name')->get();

        $playlist = PlayList::findOrFail($id);

        return view('playlist', compact('djs', 'categories', 'playlist'));
    }

    /**
     * Download the specified resource
     */
    public function download(string $id) {
        $playlist = PlayList::find($id);
        $zip = new ZipArchive();
        $zipFileName = '' . $playlist->name . '.zip';
        $zipFilePath = storage_path('app/public/files/zip' . $zipFileName);

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            return response()->json(['error' => 'No se pudo crear el archivo ZIP'], 500);
        }

        $items = $playlist->items()->get();

        $items = $items->filter(function ($item) {
            return pathinfo(Storage::disk('s3')->url($item->file_path), PATHINFO_EXTENSION) !== 'zip';
        });

        foreach ($items as $item) {
            $path = Storage::disk('s3')->url($item->file_path);
            if (Storage::disk('s3')->exists($path)) {
                $fullname = $item->title . '.' . pathinfo($path, PATHINFO_EXTENSION);
                $zip->addFile($path, $fullname);
            } else {
                return response()->json(['error' => 'El archivo ' . $path . ' no se ha encontrado.'], 500);
            }
        }

        $zip->close();

        if (!file_exists($zipFilePath)) {
            return response()->json(['error' => 'El archivo ' . $zipFileName . ' no se ha creado.'], 500);
        }

        $download = new Download();
        $download->user_id = auth()->check() ? auth()->user()->id : null;
        $download->playlist_id = $playlist->id;
        $download->save();

        return Response::download($zipFilePath)->deleteFileAfterSend(true);
    }

    /**
     * Download a item of the specifie resource
     */
    public function download_item(string $id, string $itemId) {
        $item = PlayList::find($id)->items()->where('id', $itemId)->first();

        $download = new Download();
        $download->user_id = auth()->check() ? auth()->user()->id : null;
        $download->playlist_item_id = $item->id;
        $download->save();

        $path = Storage::disk('s3')->url($item->file_path);

        if (!Storage::disk('s3')->exists($path)) {
            return response()->json(['error' => 'El archivo ' . $path . ' no se ha encontrado.'], 500);
        }
        
        $fullname = $item->title . '.' . pathinfo($path, PATHINFO_EXTENSION);

        return Response::download(Storage::disk('s3')->path($item->file_path), $fullname);
    }

    /**
     * Add Playlist to cart
     */
    
    public function addToCart(string $id){
        $cart = Cart::get_current_cart();

        $cart->cart_items()->create([
            'play_list_id' => $id,
            'amount' => PlayList::find($id)->price,
        ]);

        return redirect()->back()->with('success','Playlist añadido al carrito.');
    }

    /**
     * Add Playlist item to cart
     */
    public function addItemToCart(string $id, string $itemId){
        $cart = Cart::get_current_cart();

        $cart->cart_items()->create([
            'play_list_item_id' => $itemId,
            'amount' => PlayListItem::find($itemId)->price,
        ]);

        return redirect()->back()->with('success','Elemento añadido al carrito.');
    }

    /**
     * Remove Playlist from cart
     */
    public function removeToCart(string $id){
        $cart = Cart::get_current_cart();

        $cartItem = $cart->cart_items()->where('play_list_id', $id)->first();

        if($cartItem){
            $cartItem->delete();
            return redirect()->back()->with('success','Playlist eliminado del carrito.');
        }

        return redirect()->back()->with('error','La playlist no está en su carrito.');
    }

    /**
     * Remove Playlist item from cart
     */
    public function removeItemToCart(string $id, string $itemId){
        $cart = Cart::get_current_cart();

        $cartItem = $cart->cart_items()->where('play_list_item_id', $itemId)->first();

        if($cartItem){
            $cartItem->delete();
            return redirect()->back()->with('success','Elemento eliminado del carrito.');
        }

        return redirect()->back()->with('error','El elemento no está en su carrito.');
    }
}
