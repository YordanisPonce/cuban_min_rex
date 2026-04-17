<?php

namespace App\Http\Controllers;

use App\Enums\FolderTypeEnum;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Download;
use App\Models\PlayList;
use App\Models\User;
use App\Models\Cart;
use App\Models\Folder;
use App\Models\PlayListItem;
use Carbon\Carbon;
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
        $name = request()->get("title");
        $dj = request()->get("dj");

        $playlists = PlayList::whereHas('items')->orderBy('created_at')->paginate(4)->withQueryString();

        $folders = Folder::where('type', FolderTypeEnum::PLAYLIST->value)->take(4)->get();

        $banners = Banner::where('active', true)->pluck('path');

        if($banners->count() > 0) 
        {
            $banners = $banners->toArray();

            $banners = array_map(function ($banner) {
                return Storage::disk('s3')->url($banner ?? '');
            }, $banners);

        } else {
            $banners = [asset('assets/img/hero-base.jpeg')];
        }

        $index = 4;

        return view('playlists', compact('index', 'playlists', 'folders', 'banners'));
    }

    /**
     * Display a listing of the resource.
     */
    public function list()
    {
        $name = request()->get("title");
        $dj = request()->get("dj");
        $folder = request()->get("folder");

        $playlists = PlayList::whereHas('items');
        
        if($name){
            $playlists = $playlists->where('name', 'like', '%'.$name.'%');
        }

        if($dj){
            $playlists = $playlists->whereHas('user', function($q) use ($dj) {
                $q->where('name',  'like', '%'.str_replace('_', ' ', $dj).'%');
            });
        }

        if($folder){
            $playlists = $playlists->whereHas('folder', function($q) use ($folder) {
                $q->where('name',  'like', '%'.str_replace('_', ' ', $folder).'%');
            });
        }
    
        $playlists = $playlists->paginate(10)->withQueryString();

        $folders = Folder::where('type', FolderTypeEnum::PLAYLIST->value)->get();

        $djs = User::whereHas('playlists')->get();

        $banners = Banner::where('active', true)->pluck('path');

        if($banners->count() > 0) 
        {
            $banners = $banners->toArray();

            $banners = array_map(function ($banner) {
                return Storage::disk('s3')->url($banner ?? '');
            }, $banners);

        } else {
            $banners = [asset('assets/img/hero-base.jpeg')];
        }

        $index = 4;

        return view('playlists-list', compact('index', 'playlists', 'djs','folders', 'banners'));
    }

    public function folders()
    {
        $name = request()->get("title");

        $folders = Folder::whereHas('playlists');
        
        if($name){
            $folders = $folders->where('name', 'like', '%'.$name.'%');
        }
    
        $folders = $folders->paginate(10)->withQueryString();

        $banners = Banner::where('active', true)->pluck('path');

        if($banners->count() > 0) 
        {
            $banners = $banners->toArray();

            $banners = array_map(function ($banner) {
                return Storage::disk('s3')->url($banner ?? '');
            }, $banners);

        } else {
            $banners = [asset('assets/img/hero-base.jpeg')];
        }

        $index = 4;

        return view('folders', compact('index', 'folders', 'banners'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $name)
    {

        $playlist = PlayList::where('name',  str_replace('_', ' ', $name))->first();

        $tracks = $playlist->items()->get()->transform(function ($track) use($playlist) {
            return [
                'id' => (string) $track->id,
                'date' => $track->created_at,
                'artist' => $playlist->user->name,
                'title' => $track->title,
                'img' => $playlist->cover ? $playlist->getCoverUrl() : $playlist->user->photo ?? config('app.logo_alter'),
                'bpm' => null,
                'duration' => 120,
                'genre' => null,
                'badge' => null,
                'price' => $track->price,
                'url' => $track->file_path ? Storage::disk('s3')->url($track->file_path) : '',
                'downloads' => $track->downloads->count(),
                'canDownload' => $playlist->canBeDownload(),
                'downloadLink' => $playlist->canBeDownload() ? route('playlist.download_item', [$playlist->name, $track->id]) : null,
                'addToCart' => route('playlist.add.item.cart', [$playlist->name, $track->id]),
            ];
        });

        $similar = [];

        if($playlist){
            $similar = PlayList::where('id', '!=' ,$playlist->id)->where('folder_id', $playlist->folder?->id ?? null)->where('user_id', $playlist->user->id)
                ->orderBy('created_at', 'desc')->take(4)->get();

            $similar = $similar->transform(function ($s) {
                return [
                    'id' => (string) $s->id,
                    'date' => $s->created_at,
                    'title' => $s->name,
                    'img' => $s->cover ? $s->getCoverUrl() : $s->user->photo ?? config('app.logo_alter'),
                    'tracks' => $s->items->count(),
                    'url' => route('playlist.show', str_replace(' ', '_', $s->name)),
                ];
            });
        }

        $banners = Banner::where('active', true)->pluck('path');

        if($banners->count() > 0) 
        {
            $banners = $banners->toArray();

            $banners = array_map(function ($banner) {
                return Storage::disk('s3')->url($banner ?? '');
            }, $banners);

        } else {
            $banners = [asset('assets/img/hero-base.jpeg')];
        }

        $index = 4;

        return view('playlist', compact('playlist', 'index', 'tracks', 'similar', 'banners'));
    }

    /**
     * Download the specified resource
     */
    public function download(string $name) {
        $playlist = PlayList::where('name',  str_replace('_', ' ', $name))->first();
        if($playlist->canBeDownload()){
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
            $download->play_list_id = $playlist->id;
            $download->save();

            return Response::download($zipFilePath)->deleteFileAfterSend(true);
        }
        return redirect()->back()->with('error', 'Ha superados las descargas por mes permitida por su plan, considere mejorar su plan.'); 
    }

    /**
     * Download a item of the specifie resource
     */
    public function download_item(string $name, string $itemId) {
        $playlist = PlayList::where('name',  str_replace('_', ' ', $name))->first();
        if($playlist->canBeDownload()){
            $item = $playlist->items()->where('id', $itemId)->first();

            $download = new Download();
            $download->user_id = auth()->check() ? auth()->user()->id : null;
            $download->play_list_item_id = $item->id;
            $download->save();

            $path = Storage::disk('s3')->url($item->file_path);

            if (!Storage::disk('s3')->exists($path)) {
                return response()->json(['error' => 'El archivo ' . $path . ' no se ha encontrado.'], 500);
            }
            
            $fullname = $item->title . '.' . pathinfo($path, PATHINFO_EXTENSION);

            return Response::download(Storage::disk('s3')->path($item->file_path), $fullname);
        }
        return redirect()->back()->with('error', 'Ha superados las descargas por mes permitida por su plan, considere mejorar su plan.'); 
    }

    /**
     * Add Playlist to cart
     */
    
    public function addToCart(string $name){
        $playlist = PlayList::where('name',  str_replace('_', ' ', $name))->first();
        
        $cart = Cart::get_current_cart();

        $cart->cart_items()->create([
            'play_list_id' => $playlist->id,
            'amount' => $playlist->price,
        ]);

        return redirect()->back()->with('success','Playlist añadido al carrito.');
    }

    /**
     * Add Playlist item to cart
     */
    public function addItemToCart(string $name, string $itemId){
        $playlist = PlayList::where('name',  str_replace('_', ' ', $name))->first();

        $cart = Cart::get_current_cart();

        $cart->cart_items()->create([
            'play_list_item_id' => $itemId,
            'amount' => $playlist->price,
        ]);

        return redirect()->back()->with('success','Elemento añadido al carrito.');
    }

    /**
     * Remove Playlist from cart
     */
    public function removeToCart(string $name){
        $playlist = PlayList::where('name',  str_replace('_', ' ', $name))->first();

        $cart = Cart::get_current_cart();

        $cartItem = $cart->cart_items()->where('play_list_id', $playlist->id)->first();

        if($cartItem){
            $cartItem->delete();
            return redirect()->back()->with('success','Playlist eliminado del carrito.');
        }

        return redirect()->back()->with('error','La playlist no está en su carrito.');
    }

    /**
     * Remove Playlist item from cart
     */
    public function removeItemToCart(string $name, string $itemId){
        $cart = Cart::get_current_cart();

        $cartItem = $cart->cart_items()->where('play_list_item_id', $itemId)->first();

        if($cartItem){
            $cartItem->delete();
            return redirect()->back()->with('success','Elemento eliminado del carrito.');
        }

        return redirect()->back()->with('error','El elemento no está en su carrito.');
    }
}
