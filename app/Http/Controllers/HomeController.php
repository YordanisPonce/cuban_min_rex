<?php

namespace App\Http\Controllers;

use App\Enums\NotificationTypeEnum;
use App\Enums\SectionEnum;
use App\Models\Banner;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Collection;
use App\Models\File;
use App\Models\Follow;
use App\Models\NotificationSettings;
use App\Models\Plan;
use App\Models\PlayList;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserNotification;
use App\Notifications\ContactNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index()
    {
        $pageTitle = "Inicio";

        $newItems = File::audios()
            ->where('isExclusive', false)
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->where('status', 'active')
            ->whereJsonContains('sections', SectionEnum::MAIN->value)
            ->orderBy('created_at', 'desc')->take(5)->get();
            
        if ($newItems->count() == 0) {
            $newItems = File::audios()
                ->where('status', 'active')
                ->where('isExclusive', false)
                ->whereJsonContains('sections', SectionEnum::MAIN->value)
                ->orderBy('created_at', 'desc')->take(5)->get();
        }

        $newItems->transform(function ($file) {
            return [
                'id' => (string) $file->id,
                'date' => $file->created_at,
                'artist' => $file->user->name,
                'title' => $file->name,
                'img' => $file->poster ?? $file->user->photo ?? config('app.logo_alter'),
                'bpm' => $file->bpm,
                'duration' => 120,
                'genre' => $file->categories->pluck('name')->implode(' · ') ?? '',
                'price' => $file->price,
                'url' => Storage::disk('s3')->url($file->file),
                'isNew' => Carbon::parse($file->created_at)->isCurrentDay(),
                'canDownload' => auth()->check() && auth()->user()->hasActivePlan(),
                'downloadLink' => auth()->check() && auth()->user()->hasActivePlan() ? route('file.download', $file->id) : null,
                'addToCart' => route('file.add.cart', $file->id),
            ];
        });

        $tops = User::join('files', 'users.id', '=', 'files.user_id')
            ->join('category_files', 'category_files.file_id', 'files.id')
            ->selectRaw('users.name, SUM(files.download_count) as downloads, users.photo' )
            ->groupBy(['users.name', 'users.photo'])
            ->orderBy('downloads', 'desc')->take(5)
            ->get();
        $tops->transform(function ($dj) {
            return [
                'name' => $dj->name,
                'genres' => '',
                'img' => $dj->photo ?? config('app.logo_alter'),
                'downloads' => $dj->downloads,
                'route' => route('dj', str_replace(' ', '_', $dj->name)),
            ];
        });

        $playlists = PlayList::join('downloads', 'play_lists.id', 'downloads.play_list_id')
            ->join('users', 'play_lists.user_id', 'users.id')
            ->selectRaw('play_lists.name as title, users.name as dj, count(downloads.play_list_id) as downloads, play_lists.cover as cover, users.photo as photo')
            ->groupBy(['title', 'dj', 'cover', 'photo'])
            ->orderBy('downloads', 'desc')->take(3)->get();

        $playlists->transform(function ($playlist) {
            return [
                'title' => $playlist->title,
                'sub' => $playlist->dj,
                'tag' => '',
                'genre' => $playlist->folder->name ?? '',
                'imgs' => [$playlist->cover ?? $playlist->photo ?? config('app.logo_alter')],
                'downloads' => $playlist->downloads,
                'route' => route('playlist.show', str_replace(' ', '_', $playlist->title)),
            ];
        });

        $geners = Category::join('category_files', 'categories.id', 'category_files.category_id')
            ->join('files', 'files.id', 'category_files.file_id')
            ->join('downloads', 'files.id', 'downloads.file_id')
            ->selectRaw('categories.id as id, categories.name as name, count(downloads.file_id) as downloads')
            ->groupBy(['id', 'name'])
            ->orderBy('downloads')->take(12)->get();

        $geners->transform(function ($gener) {
            return [
                'name' => $gener->name,
                'icon' => $gener->id%2===0 ? 'fa-headphones' : 'fa-music',
                'route' => route('remixes', ['genre' => $gener->name]),
            ];
        }); 

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

        $index = 0;

        return view('home', compact('pageTitle', 'geners', 'index', 'newItems', 'playlists', 'tops', 'banners'));
    }

    public function faq()
    {

        $categories = Category::where('show_in_landing', true)->orderBy('name')->get();
        $djs = User::whereHas('files')->orderBy('name')->get();

        $recentDjs = User::whereNot('role', 'user')->orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });

        return view('faq', compact('djs', 'categories', 'recentCategories', 'recentCollections'));
    }

    public function contact()
    {
        $categories = Category::where('show_in_landing', true)->orderBy('name')->get();

        $djs = User::whereHas('files')->orderBy('name')->get();

        $recentDjs = User::whereNot('role', 'user')->orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });

        return view('contact', compact('djs', 'categories', 'recentCategories', 'recentCollections'));
    }

    public function radio()
    {

        $mixes = File::audios()->section(SectionEnum::CUBANDJS->value)
            ->where('status', 'active')
            ->whereNot('original_file', 'LIKE', '%.zip')
            ->orderBy('created_at', 'desc')
            ->take(10)->get();

        $mixes->transform(function ($file) {
            return [
                'id' => (string) $file->id,
                'date' => $file->created_at,
                'artist' => $file->user?->name ?? 'Desconocido',
                'type' => 'Intro',
                'title' => $file->name,
                'badge' => null,
                'img' => $file->poster ?? $file->user->photo ?? config('app.logo_alter'),
                'bpm' => $file->bpm,
                'key' => $file->musical_note ?? '7A',
                'duration' => 120,
                'genre' => $file->categories->pluck('name')->toArray() ?? ['DESCONOCIDO'],
                'price' => $file->price,
                'url' => Storage::disk('s3')->url($file->file),
                'isNew' => Carbon::parse($file->created_at)->isCurrentDay(),
                'downloads' => $file->sales->count(),
                'canDownload' => null,
                'downloadLink' => null,
                'usd_pay' => route('radio.file.pay', $file->id),
                'cup_pay' => route('payment.cup.form', $file->id),
            ];
        });

        $lives = File::audios()->section(SectionEnum::CUBANDJS_LIVE_SESSIONS->value)
            ->where('status', 'active')
            ->whereNot('original_file', 'LIKE', '%.zip')
            ->orderBy('created_at', 'desc')
            ->take(10)->get();
        
        $lives->transform(function ($file) {
            return [
                'id' => (string) $file->id,
                'date' => $file->created_at,
                'artist' => $file->user?->name ?? 'Desconocido',
                'type' => 'Intro',
                'title' => $file->name,
                'badge' => null,
                'img' => $file->poster ?? $file->user->photo ?? config('app.logo_alter'),
                'bpm' => $file->bpm,
                'key' => $file->musical_note ?? '7A',
                'duration' => 120,
                'genre' => $file->categories->pluck('name')->toArray() ?? ['DESCONOCIDO'],
                'price' => $file->price,
                'url' => Storage::disk('s3')->url($file->file),
                'isNew' => Carbon::parse($file->created_at)->isCurrentDay(),
                'downloads' => $file->sales->count(),
                'canDownload' => null,
                'downloadLink' => null,
                'usd_pay' => route('radio.file.pay', $file->id),
                'cup_pay' => route('payment.cup.form', $file->id),
            ];
        });

        $index = 6;

        return view('radio', compact('index', 'lives', 'mixes'));
    }
    
    public function radio_remixes(Request $request)
    {
        $title = request()->get('title');
        $genre = request()->get('genre');
        $dj = request()->get('dj');

        $tracks = File::audios()
            ->where('status', 'active')
            ->where('isExclusive', false);

        if($title) {
            $tracks = $tracks->where('name',  'like', '%' . $title . '%');
        }

        if($dj){
            $tracks = $tracks->whereHas('user',  function($q) use ($dj) {
                $q->where('name',  'like', '%' . str_replace('_',' ', $dj) . '%');
            });
        }

        if($genre){
            $tracks = $tracks->whereJsonContains('sections', $genre);
        } else {
            $tracks = $tracks->whereJsonContains('sections', SectionEnum::CUBANDJS->value)
            ->orWhereJsonContains('sections', SectionEnum::CUBANDJS_LIVE_SESSIONS->value);
        }

        $tracks = $tracks->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $tracks->getCollection()->transform(function ($file) {
            return [
                'id' => (string) $file->id,
                'date' => $file->created_at,
                'artist' => $file->user?->name ?? 'Desconocido',
                'type' => 'Intro',
                'title' => $file->name,
                'badge' => null,
                'img' => $file->poster ?? $file->user->photo ?? config('app.logo_alter'),
                'bpm' => $file->bpm,
                'key' => $file->musical_note ?? '7A',
                'duration' => 120,
                'genre' => $file->categories->pluck('name')->toArray() ?? ['DESCONOCIDO'],
                'price' => $file->price,
                'url' => Storage::disk('s3')->url($file->file),
                'isNew' => Carbon::parse($file->created_at)->isCurrentDay(),
                'downloads' => $file->sales->count(),
                'canDownload' => null,
                'downloadLink' => null,
                'usd_pay' => route('radio.file.pay', $file->id),
                'cup_pay' => route('payment.cup.form', $file->id),
            ];
        });

        $djs = User::whereHas('files', function($q){
            $q->audios()->where('status', 'active')
            ->whereJsonContains('sections', SectionEnum::CUBANDJS->value)
            ->orWhereJsonContains('sections', SectionEnum::CUBANDJS_LIVE_SESSIONS->value);
        })->get();

        $genres = [
            [
                'value' => SectionEnum::CUBANDJS->value,
                'name' => SectionEnum::getTransformName(SectionEnum::CUBANDJS->value),
            ],
            [
                'value' => SectionEnum::CUBANDJS_LIVE_SESSIONS->value,
                'name' => SectionEnum::getTransformName(SectionEnum::CUBANDJS_LIVE_SESSIONS->value),
            ]
        ];

        $index = 6;

        return view('radio-remixes', compact('index', 'tracks', 'djs', 'genres'));
    }

    public function plan()
    {
        
        $plans = Plan::orderBy('price')->get();

        $index = 7;

        return view('plans', compact('index', 'plans'));
    }

    public function djs()
    {
        $name = request()->get('search');

        $djs = User::whereHas('files', function($q){
            $q->section(SectionEnum::MAIN->value);
        });

        if ($name) {
            $djs = $djs->where('name', 'like', '%' . $name . '%');
        }
        
        $djs = $djs->orderBy('name')->paginate(10)->withQueryString();

        $djs->transform(function ($dj) {
            return [
                'name' => $dj->name,
                'genres' => $dj->files()->with('categories')->get()->pluck('categories')->flatten()->pluck('name')->unique()->implode(' · '),
                'img' => $dj->photo ?? config('app.logo_alter'),
                'downloads' => $dj->files()->sum('download_count'),
                'route' => route('dj', str_replace(' ', '_', $dj->name)),
            ];
        });

        $index = 1;

        return view('djs', compact('index', 'djs'));
    }

    public function dj($name)
    {
        $dj = User::where('users.name', 'like', str_replace('_', ' ', $name))->first();

        $index = 1;

        $isFollow = false;
        $isNtf = false;

        if (auth()->check() && auth()->user()->hasActivePlan()) {
            $follow = Follow::where('follower_id', auth()->user()->id)->where('follow_id', $dj->id)->first();
            $ntf = UserNotification::where('user_id', auth()->user()->id)->where('dj_id', $dj->id)->first();
            if($follow){
                $isFollow = true;
            }
            if($ntf){
                $isNtf = true;
            }
        }

        return view('dj-profile', compact('index', 'dj', 'isFollow', 'isNtf'));
    }

    public function sendContactForm(Request $request)
    {

        $name = $request->fullname ?? 'Anónimo';
        $email = $request->email ?? 'Desconocido';
        $message = $request->message ?? 'Mensaje vacío';

        try {
            $admins = User::where('role', 'admin')->get();

            foreach ($admins as $admin) {
                $admin->notify(new ContactNotification($name, $email, $message));
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Se ha enviado el formulario exitosamente, el personal de soporte se pondra en contacto con usted.');
    }

    public function remixes(Request $request)
    {
        $title = request()->get('title');
        $genre = request()->get('genre');
        $bpm = request()->get('bpm');
        $dj = request()->get('dj');

        $tracks = File::audios()
            ->where('status', 'active')
            ->where('isExclusive', false)
            ->whereJsonContains('sections', SectionEnum::MAIN->value);

        $exclusives = File::audios()
            ->where('status', 'active')
            ->where('isExclusive', true)
            ->whereJsonContains('sections', SectionEnum::MAIN->value)
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->take(3)->get();

        if($title) {
            $tracks = $tracks->where('name',  'like', '%' . $title . '%');
        }

        if($dj){
            $tracks = $tracks->whereHas('user',  function($q) use ($dj) {
                $q->where('name',  'like', '%' . str_replace('_',' ', $dj) . '%');
            });
        }

        if($genre){
            $tracks = $tracks->whereHas('categories',  function($q) use ($genre) {
                $q->where('name',  'like', '%' . str_replace('_',' ', $genre) . '%');
            });
        }

        if($bpm){
            $tracks = $tracks->where('bpm', 'like', '%'.$bpm.'%');
        }

        $tracks = $tracks->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $tracks->getCollection()->transform(function ($file) {
            return [
                'id' => (string) $file->id,
                'date' => $file->created_at,
                'artist' => $file->user?->name ?? 'Desconocido',
                'type' => 'Intro',
                'title' => $file->name,
                'badge' => null,
                'img' => $file->poster ?? $file->user->photo ?? config('app.logo_alter'),
                'bpm' => $file->bpm,
                'key' => $file->musical_note ?? '7A',
                'duration' => 120,
                'genre' => $file->categories->pluck('name')->toArray() ?? ['DESCONOCIDO'],
                'price' => $file->price,
                'url' => Storage::disk('s3')->url($file->file),
                'isNew' => Carbon::parse($file->created_at)->isCurrentDay(),
                'downloads' => $file->download_count,
                'canDownload' => auth()->check() && auth()->user()->hasActivePlan(),
                'downloadLink' => auth()->check() && auth()->user()->hasActivePlan() ? route('file.download', $file->id) : null,
                'addToCart' => route('file.add.cart', $file->id),
            ];
        });

        $djs = User::whereHas('files', function($q){
            $q->audios()->where('status', 'active')
            ->whereJsonContains('sections', SectionEnum::MAIN->value);
        })->get();

        $genres = Category::whereHas('files', function($q){
            $q->audios()->where('status', 'active')
            ->whereJsonContains('sections', SectionEnum::MAIN->value);
        })->orderBy('name')->get();

        $bpms = File::audios()->where('status', 'active')
            ->whereJsonContains('sections', SectionEnum::MAIN->value)
            ->groupBy('bpm')
            ->orderBy('bpm')
            ->get('bpm');

        $index = 2;

        return view('remixes', compact('index', 'tracks', 'djs', 'genres', 'bpms', 'exclusives'));
    }

    public function exclusiveRemixes(Request $request)
    {
        $title = request()->get('title');
        $genre = request()->get('genre');
        $bpm = request()->get('bpm');
        $dj = request()->get('dj');

        $tracks = File::audios()
            ->where('status', 'active')
            ->where('isExclusive', true)
            ->whereJsonContains('sections', SectionEnum::MAIN->value);

        if($title) {
            $tracks = $tracks->where('name',  'like', '%' . $title . '%');
        }

        if($dj){
            $tracks = $tracks->whereHas('user',  function($q) use ($dj) {
                $q->where('name',  'like', '%' . str_replace('_',' ', $dj) . '%');
            });
        }

        if($genre){
            $tracks = $tracks->whereHas('categories',  function($q) use ($genre) {
                $q->where('name',  'like', '%' . str_replace('_',' ', $genre) . '%');
            });
        }

        if($bpm){
            $tracks = $tracks->where('bpm', 'like', '%'.$bpm.'%');
        }

        $tracks = $tracks->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $tracks->getCollection()->transform(function ($file) {
            return [
                'id' => (string) $file->id,
                'date' => $file->created_at,
                'artist' => $file->user?->name ?? 'Desconocido',
                'type' => 'Intro',
                'title' => $file->name,
                'badge' => null,
                'img' => $file->poster ?? $file->user->photo ?? config('app.logo_alter'),
                'bpm' => $file->bpm,
                'key' => $file->musical_note ?? '7A',
                'duration' => 120,
                'genre' => $file->categories->pluck('name')->toArray() ?? ['DESCONOCIDO'],
                'price' => $file->price,
                'url' => Storage::disk('s3')->url($file->file),
                'isNew' => Carbon::parse($file->created_at)->isCurrentDay(),
                'downloads' => $file->sales->count(),
                'canDownload' => false,
                'downloadLink' => null,
                'addToCart' => route('file.add.cart', $file->id),
            ];
        });

        $djs = User::whereHas('files', function($q){
            $q->audios()->where('status', 'active')
            ->whereJsonContains('sections', SectionEnum::MAIN->value)
            ->where('isExclusive', true);
        })->get();

        $genres = Category::whereHas('files', function($q){
            $q->audios()->where('status', 'active')
            ->whereJsonContains('sections', SectionEnum::MAIN->value)
            ->where('isExclusive', true);
        })->orderBy('name')->get();

        $bpms = File::audios()->where('status', 'active')
            ->whereJsonContains('sections', SectionEnum::MAIN->value)
            ->where('isExclusive', true)
            ->groupBy('bpm')
            ->orderBy('bpm')
            ->get('bpm');

        $index = 2;

        return view('exclusive-remixes', compact('index', 'tracks', 'djs', 'genres', 'bpms'));
    }

    public function exclusiveVideos(Request $request)
    {
        $title = request()->get('title');
        $genre = request()->get('genre');
        $bpm = request()->get('bpm');
        $dj = request()->get('dj');

        $tracks = File::videos()
            ->where('status', 'active')
            ->where('isExclusive', true)
            ->whereJsonContains('sections', SectionEnum::MAIN->value);

        if($title) {
            $tracks = $tracks->where('name',  'like', '%' . $title . '%');
        }

        if($dj){
            $tracks = $tracks->whereHas('user',  function($q) use ($dj) {
                $q->where('name',  'like', '%' . str_replace('_',' ', $dj) . '%');
            });
        }

        if($genre){
            $tracks = $tracks->whereHas('categories',  function($q) use ($genre) {
                $q->where('name',  'like', '%' . str_replace('_',' ', $genre) . '%');
            });
        }

        if($bpm){
            $tracks = $tracks->where('bpm', 'like', '%'.$bpm.'%');
        }

        $tracks = $tracks->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $tracks->getCollection()->transform(function ($file) {
            return [
                'id' => (string) $file->id,
                'date' => $file->created_at,
                'artist' => $file->user?->name ?? 'Desconocido',
                'type' => 'Intro',
                'title' => $file->name,
                'badge' => null,
                'img' => $file->poster ?? $file->user->photo ?? config('app.logo_alter'),
                'bpm' => $file->bpm,
                'key' => $file->musical_note ?? '7A',
                'duration' => 120,
                'genre' => $file->categories->pluck('name')->toArray() ?? ['DESCONOCIDO'],
                'price' => $file->price,
                'url' => Storage::disk('s3')->url($file->file),
                'isNew' => Carbon::parse($file->created_at)->isCurrentDay(),
                'downloads' => $file->sales->count(),
                'canDownload' => false,
                'downloadLink' => null,
                'addToCart' => route('file.add.cart', $file->id),
            ];
        });

        $djs = User::whereHas('files', function($q){
            $q->videos()->where('status', 'active')
            ->whereJsonContains('sections', SectionEnum::MAIN->value)
            ->where('isExclusive', true);
        })->get();

        $genres = Category::whereHas('files', function($q){
            $q->videos()->where('status', 'active')
            ->whereJsonContains('sections', SectionEnum::MAIN->value)
            ->where('isExclusive', true);
        })->orderBy('name')->get();

        $bpms = File::videos()->where('status', 'active')
            ->where('isExclusive', true)
            ->whereJsonContains('sections', SectionEnum::MAIN->value)
            ->groupBy('bpm')
            ->orderBy('bpm')
            ->get('bpm');

        $index = 3;

        return view('exclusive-videos', compact('index', 'tracks', 'djs', 'genres', 'bpms'));
    }

    public function videos(Request $request)
    {
        $title = request()->get('title');
        $genre = request()->get('genre');
        $bpm = request()->get('bpm');
        $dj = request()->get('dj');

        $tracks = File::videos()
            ->where('status', 'active')
            ->where('isExclusive', false)
            ->whereJsonContains('sections', SectionEnum::MAIN->value);
        
        $exclusives = File::videos()
            ->where('status', 'active')
            ->where('isExclusive', true)
            ->whereJsonContains('sections', SectionEnum::MAIN->value)
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->take(3)->get();

        if($title) {
            $tracks = $tracks->where('name',  'like', '%' . $title . '%');
        }

        if($dj){
            $tracks = $tracks->whereHas('user',  function($q) use ($dj) {
                $q->where('name',  'like', '%' . str_replace('_',' ', $dj) . '%');
            });
        }

        if($genre){
            $tracks = $tracks->whereHas('categories',  function($q) use ($genre) {
                $q->where('name',  'like', '%' . str_replace('_',' ', $genre) . '%');
            });
        }

        if($bpm){
            $tracks = $tracks->where('bpm', 'like', '%'.$bpm.'%');
        }

        $tracks = $tracks->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $tracks->getCollection()->transform(function ($file) {
            return [
                'id' => (string) $file->id,
                'date' => $file->created_at,
                'artist' => $file->user?->name ?? 'Desconocido',
                'type' => 'Intro',
                'title' => $file->name,
                'badge' => null,
                'img' => $file->poster ?? $file->user->photo ?? config('app.logo_alter'),
                'bpm' => $file->bpm,
                'key' => $file->musical_note ?? '7A',
                'duration' => 120,
                'genre' => $file->categories->pluck('name')->toArray() ?? ['DESCONOCIDO'],
                'price' => $file->price,
                'url' => Storage::disk('s3')->url($file->file),
                'isNew' => Carbon::parse($file->created_at)->isCurrentDay(),
                'downloads' => $file->download_count,
                'canDownload' => auth()->check() && auth()->user()->hasActivePlan(),
                'downloadLink' => auth()->check() && auth()->user()->hasActivePlan() ? route('file.download', $file->id) : null,
                'addToCart' => route('file.add.cart', $file->id),
            ];
        });

        $djs = User::whereHas('files', function($q){
            $q->videos()->where('status', 'active')
            ->whereJsonContains('sections', SectionEnum::MAIN->value);
        })->get();

        $genres = Category::whereHas('files', function($q){
            $q->videos()->where('status', 'active')
            ->whereJsonContains('sections', SectionEnum::MAIN->value);
        })->get();

        $bpms = File::videos()->where('status', 'active')
            ->whereJsonContains('sections', SectionEnum::MAIN->value)
            ->groupBy('bpm')
            ->orderBy('bpm')
            ->get('bpm');

        $index = 3;

        return view('videos', compact('index', 'tracks', 'djs', 'genres', 'bpms', 'exclusives'));
    }

    public function cart(Request $request)
    {
        $index = 999;
        
        $cart = Cart::get_current_cart();

        return view('cart', compact('index', 'cart'));
    }

    public function legal()
    {
        $index = 999;

        $legalTexts = \App\Models\LegalText::first();

        $title = 'Aviso Legal';

        $text = $legalTexts?->legal ?? 'No se ha proporcionado un aviso legal.';

        return view('legal', compact('index', 'title', 'text'));
    }

    public function privacy()
    {
        $index = 999;

        $legalTexts = \App\Models\LegalText::first();

        $title = 'Política de Privacidad';

        $text = $legalTexts?->privacy ?? 'No se ha proporcionado una política de privacidad.';

        return view('legal', compact('index', 'title', 'text'));
    }

    public function cookies()
    {
        $index = 999;

        $legalTexts = \App\Models\LegalText::first();

        $title = 'Política de Cookies';

        $text = $legalTexts?->cookies ?? 'No se ha proporcionado una política de cookies.';

        return view('legal', compact('index', 'title', 'text'));
    }

    public function terms()
    {
        $index = 999;

        $legalTexts = \App\Models\LegalText::first();

        $title = 'Términos y Condiciones';

        $text = $legalTexts?->terms ?? 'No se han proporcionado términos y condiciones.';

        return view('legal', compact('index', 'title', 'text'));
    }

    public function ntfs(){

        $index = 999;

        $notifications = auth()->user()->notifications;

        $prefers = auth()->user()->ntfs_prefs;
        if (!$prefers) {
            $prefers = new NotificationSettings();
            $prefers->user_id = auth()->user()->id;
            $prefers->save();
        }

        return view('notification-center', compact('index','notifications','prefers'));
    }
    

    public function ntfs_setting_update(Request $request){

        $index = 999;

        $prefers = auth()->user()->ntfs_prefs;

        if (!$prefers) {
            $prefers = new NotificationSettings();
            $prefers->user_id = auth()->user()->id;
            $prefers->save();
        }

        $prefers->update([
            'new_remixes' => $request->new_remixes ? true : false,
            'new_playlists' => $request->new_playlists ? true : false,
            'new_followers' => $request->new_followers ? true : false,
            'promos' => $request->promos ? true : false,
        ]);

        return redirect()->back()->with('success', 'Preferencias actualizadas correctamente');
    }
}
