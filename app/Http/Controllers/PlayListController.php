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
use App\Models\Order;
use App\Jobs\BuildPlaylistZipJob;
use App\Models\PlaylistZipRequest;
use App\Services\PlaylistZipBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PlayListController extends Controller
{
    private const ASYNC_ZIP_THRESHOLD_BYTES = 314572800; // 300 MB
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $name = request()->get("title");
        $dj = request()->get("dj");

        $playlists = PlayList::whereHas('items')->orderBy('created_at', 'desc')->paginate(5)->withQueryString();

        $folders = Folder::where('type', FolderTypeEnum::PLAYLIST->value)->orderBy('created_at', 'desc')->take(5)->get();

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
    
        $playlists = $playlists->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

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
    
        $folders = $folders->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

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
                'downloadLink' => $playlist->canBeDownload() ? route('playlist.download_item', [str_replace(' ', '_' , $playlist->name), $track->id]) : null,
                'addToCart' => route('playlist.add.item.cart', [str_replace(' ', '_' , $playlist->name), $track->id]),
                'info' => route('playlist.item.info', [str_replace(' ','_', $playlist->name),str_replace(' ','_', $track->title)]),
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
     * Display the specified resource info.
     */
    public function info(string $playlist, string $name){
        $playlist = PlayList::where('name',  str_replace('_', ' ', $playlist))->first();

        if(!$playlist) return abort(404);

        $song = $playlist->items()->where('title', str_replace('_', ' ', $name))->first();

        if(!$song) return abort(404);

        $item = [
            'name' => $song->title,
            'poster' => $playlist->getCoverUrl(),
            'artist' => $playlist->user?->name,
            'description' => 'Perteneciente a la Playlist '.$playlist->name,
            'bpm' => null,
            'note' => null,
            'date' => $song->created_at->format('d/m/Y'),
            'price' => $song->price,
            'categories' => null,
            'intro' => $song->intro(),
            'size' => $song->getSize(),
            'ext' => $song->getExtension(),
            'downloads' => $song->downloads->count(),
            'canBeDownload' => $playlist->canBeDownload(),
            'download_link' => $playlist->canBeDownload() ? route('playlist.download_item', [str_replace(' ','_', $playlist->name),$song->id]) : null,
            'addToCart' => route('playlist.add.item.cart', [str_replace(' ','_', $playlist->name),$song->id]),
        ];

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

        return view('info', compact('item', 'banners'));
    }

    /**
     * Download the specified resource
     */
    public function download(string $name, PlaylistZipBuilder $builder)
    {
        $playlist = PlayList::where('name', str_replace('_', ' ', $name))->first();

        if (!$playlist) {
            return redirect()->back()->with('error', 'Playlist no encontrada.');
        }

        if ($authError = $this->authorizePlaylistDownload($playlist)) {
            return $authError;
        }

        $user = auth()->user();
        $zipFileName = str_replace(' ', '_', $playlist->name) . '.zip';

        $existingRequest = $this->findReusableZipRequest($user->id, $playlist->id);

        if ($existingRequest?->isReady()) {
            return $this->redirectToZipDownload($existingRequest);
        }

        if ($existingRequest?->isInProgress()) {
            return redirect()->route('playlist.download.status', [
                'playlist' => str_replace(' ', '_', $playlist->name),
                'uuid' => $existingRequest->uuid,
            ]);
        }

        $items = $playlist->items()->get();
        $totalBytes = $builder->estimateTotalBytes($items);

        if ($totalBytes >= self::ASYNC_ZIP_THRESHOLD_BYTES) {
            return $this->queuePlaylistZipDownload($playlist, $user, $zipFileName, $items->count());
        }

        return $this->buildPlaylistZipSynchronously($playlist, $user, $zipFileName, $builder);
    }

    public function downloadStatus(string $name, string $uuid): View|RedirectResponse
    {
        $zipRequest = $this->findZipRequestForUser($name, $uuid);

        if (!$zipRequest) {
            return redirect()->route('playlist.show', $name)->with('error', 'Solicitud de descarga no encontrada.');
        }

        if ($zipRequest->isReady()) {
            return $this->redirectToZipDownload($zipRequest);
        }

        return view('playlist-download-status', [
            'playlist' => $zipRequest->playList,
            'zipRequest' => $zipRequest,
            'statusUrl' => route('playlist.download.status.check', [
                'playlist' => str_replace(' ', '_', $zipRequest->playList->name),
                'uuid' => $zipRequest->uuid,
            ]),
        ]);
    }

    public function downloadStatusCheck(string $name, string $uuid): JsonResponse
    {
        $zipRequest = $this->findZipRequestForUser($name, $uuid);

        if (!$zipRequest) {
            return response()->json(['status' => 'not_found'], 404);
        }

        if ($zipRequest->isReady()) {
            return response()->json([
                'status' => 'ready',
                'download_url' => $this->temporaryZipUrl($zipRequest),
                'tracks_added' => $zipRequest->tracks_added,
                'tracks_total' => $zipRequest->tracks_total,
            ]);
        }

        if ($zipRequest->status === 'failed') {
            return response()->json([
                'status' => 'failed',
                'message' => $zipRequest->error_message ?? 'No se pudo generar el archivo ZIP.',
            ]);
        }

        return response()->json([
            'status' => $zipRequest->status,
            'tracks_added' => $zipRequest->tracks_added,
            'tracks_total' => $zipRequest->tracks_total,
        ]);
    }

    private function authorizePlaylistDownload(PlayList $playlist): ?RedirectResponse
    {
        if (!$playlist->canBeDownload()) {
            return redirect()->back()->with('error', 'Esta playlist no está disponible para descarga.');
        }

        $user = auth()->user();

        if (!$user) {
            return redirect()->back()->with('error', 'Debe iniciar sesión para descargar.');
        }

        $plan = $user->currentPlan
            ?? Order::where('user_id', $user->id)
                ->where('status', 'paid')
                ->orderBy('created_at', 'desc')
                ->first()?->plan;

        if (!$plan && $user->role !== 'admin') {
            return redirect()->back()->with('error', 'No tiene un plan activo para descargar.');
        }

        if (!$user->plan_start_at && $user->role !== 'admin') {
            return redirect()->back()->with('error', 'Su plan no está activo todavía.');
        }

        if ($user->role !== 'admin' && $user->get_current_plan_consume_downloads() >= $plan->downloads) {
            return redirect()->back()->with('error', 'Ha superado las descargas por mes permitidas por su plan, considere mejorar su plan.');
        }

        return null;
    }

    private function queuePlaylistZipDownload(
        PlayList $playlist,
        User $user,
        string $zipFileName,
        int $tracksTotal,
    ): RedirectResponse {
        $uuid = Str::random(40);

        $zipRequest = PlaylistZipRequest::create([
            'uuid' => $uuid,
            'user_id' => $user->id,
            'play_list_id' => $playlist->id,
            'status' => 'pending',
            'zip_file_name' => $zipFileName,
            'tracks_total' => $tracksTotal,
        ]);

        BuildPlaylistZipJob::dispatch($zipRequest);

        return redirect()->route('playlist.download.status', [
            'playlist' => str_replace(' ', '_', $playlist->name),
            'uuid' => $zipRequest->uuid,
        ])->with('success', 'Estamos preparando tu descarga. Esto puede tardar varios minutos.');
    }

    private function buildPlaylistZipSynchronously(
        PlayList $playlist,
        User $user,
        string $zipFileName,
        PlaylistZipBuilder $builder,
    ): RedirectResponse {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $uuid = Str::random(40);
        $localZipPath = Storage::disk('local')->path('files/zip/' . $uuid . '.zip');

        try {
            Log::debug('Creating ZIP file at: ' . $localZipPath);

            $builder->build($playlist, $localZipPath);

            $s3ZipPath = 'files/zip/temp/' . $uuid . '.zip';
            $handle = fopen($localZipPath, 'r');
            Storage::disk('s3')->put($s3ZipPath, $handle);

            if (is_resource($handle)) {
                fclose($handle);
            }

            @unlink($localZipPath);

            $this->registerPlaylistDownload($user, $playlist);

            return redirect($this->temporaryZipUrlFromPath($s3ZipPath, $zipFileName));
        } catch (\Throwable $e) {
            Log::error('Error generando ZIP de playlist: ' . $e->getMessage());
            @unlink($localZipPath);

            return redirect()->back()->with('error', 'No se pudo generar la descarga. Inténtelo de nuevo.');
        }
    }

    private function findReusableZipRequest(int $userId, int $playlistId): ?PlaylistZipRequest
    {
        return PlaylistZipRequest::query()
            ->where('user_id', $userId)
            ->where('play_list_id', $playlistId)
            ->whereIn('status', ['pending', 'processing', 'ready'])
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->first();
    }

    private function findZipRequestForUser(string $name, string $uuid): ?PlaylistZipRequest
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        return PlaylistZipRequest::query()
            ->with('playList')
            ->where('uuid', $uuid)
            ->where('user_id', $user->id)
            ->whereHas('playList', function ($query) use ($name) {
                $query->where('name', str_replace('_', ' ', $name));
            })
            ->first();
    }

    private function redirectToZipDownload(PlaylistZipRequest $zipRequest): RedirectResponse
    {
        return redirect($this->temporaryZipUrl($zipRequest));
    }

    private function temporaryZipUrl(PlaylistZipRequest $zipRequest): string
    {
        return $this->temporaryZipUrlFromPath($zipRequest->s3_path, $zipRequest->zip_file_name);
    }

    private function temporaryZipUrlFromPath(string $s3Path, string $zipFileName): string
    {
        return Storage::disk('s3')->temporaryUrl(
            $s3Path,
            now()->addMinutes(30),
            [
                'ResponseContentDisposition' => 'attachment; filename="' . $zipFileName . '"',
            ]
        );
    }

    private function registerPlaylistDownload(User $user, PlayList $playlist): void
    {
        if ($user->role === 'admin') {
            return;
        }

        $download = new Download();
        $download->user_id = $user->id;
        $download->play_list_id = $playlist->id;
        $download->amount = $user->downloads_cost();
        $download->user_amount = $user->downloads_cost() * 0.7;
        $download->admin_amount = $user->downloads_cost() * 0.1;
        $download->save();
    }

    /**
     * Download a item of the specifie resource
     */
    public function download_item(string $name, string $itemId) {
        $playlist = PlayList::where('name',  str_replace('_', ' ', $name))->first();
        if($playlist->canBeDownload()){
            $plan = null;

            if (auth()->user()->currentPlan) {
                $plan = auth()->user()->currentPlan;
            } else {
                $plan = Order::where('user_id', auth()->user()->id)->where('status', 'paid')->orderBy('created_at', 'desc')->first()?->plan;
            }

            if($plan || auth()->user()->role === 'admin'){
                if(auth()->user()->plan_start_at || auth()->user()->role === 'admin'){
                    if (auth()->user()->role === 'admin' || auth()->user()->get_current_plan_consume_downloads() < $plan->downloads) {
                        $item = $playlist->items()->where('id', $itemId)->first();

                        $path = $item->file_path;

                        /*if (!Storage::disk('s3')->exists($path)) {
                            return redirect()->back()->with('error','El archivo no se ha encontrado.');
                        }*/

                        if(auth()->check() && auth()->user()->role !== 'admin'){
                            $download = new Download();
                            $download->user_id = auth()->check() ? auth()->user()->id : null;
                            $download->play_list_item_id = $item->id;
                            $download->amount = auth()->user()->downloads_cost();
                            $download->user_amount = auth()->user()->downloads_cost() * 0.7;
                            $download->admin_amount = auth()->user()->downloads_cost() * 0.1;
                            $download->save();
                        }

                        $ext = pathinfo($path, PATHINFO_EXTENSION);
                        $downloadName = "$item->title.$ext";
                        /*return Storage::disk('s3')->download($path, $downloadName);*/
                        return downloadFileFromDisk('s3', $path, $downloadName);
                    }
                }
            }
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
