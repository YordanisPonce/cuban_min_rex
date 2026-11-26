<?php

namespace App\Http\Controllers;

use App\Enums\SectionEnum;
use App\Models\File;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(Request $request, String $name)
    {
        $category = Category::where('name', $name)->first();

        $tracks = File::audios()
            ->where('status', 'active')
            ->where('isExclusive', false)
            ->whereHas('categories', function ($query) use ($name) {
                $query->where('name', $name);
            })
            ->whereJsonContains('sections', SectionEnum::MAIN->value);

        $extras = File::audios()
            ->where('status', 'active')
            ->where('isExclusive', true)
            ->whereHas('categories', function ($query) use ($name) {
                $query->where('name', $name);
            })
            ->whereJsonContains('sections', SectionEnum::MAIN->value)
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->take(6)->get();

        if ($extras->count() === 0) {
            $extras = File::audios()
            ->where('status', 'active')
            ->where('isExclusive', true)
            ->whereHas('categories', function ($query) use ($name) {
                $query->where('name', $name);
            })
            ->whereJsonContains('sections', SectionEnum::MAIN->value)
            ->orderBy('created_at', 'desc')
            ->take(6)->get();
        }

        $extras->transform(function ($file) {
            $zips = ['zip', 'rar', '7z'];
            $vids = ['mp4', 'avi', 'mov', 'wmv', 'mkv'];
            $auds =  ['mp3', 'wav', 'ogg', 'm4a', 'flac'];
            $ext = pathinfo($file->original_file, PATHINFO_EXTENSION);
            $flag = array_search($ext,$zips) !== false ? 'pack' : (array_search($ext,$vids) !== false ? 'video' : (array_search($ext,$auds) !== false ? 'audio' : 'other'));
            return [
                'id' => (string) $file->id,
                'artist' => $file->user?->name ?? 'Desconocido',
                'title' => $file->name,
                'flag' => $flag,
                'cover' => $file->getPosterUrl() ?? $file->user->photo ?? config('app.logo_alter'),
                'bpm' => $file->bpm,
                'key' => $file->musical_note ?? '7A',
                'genre' => $file->categories->pluck('name')->toArray() ?? ['DESCONOCIDO'],
                'price' => $file->price,
                'url' => Storage::disk('s3')->url($file->file),
                'isNew' => Carbon::parse($file->created_at)->isCurrentDay(),
                'downloads' => $file->download_count,
                'canBeDownload' => $file->canBeDownload(),
                'downloadLink' => $file->canBeDownload() ? route('file.download', $file->id) : null,
                'addToCart' => route('file.add.cart', $file->id),
            ];
        });

        $tracks = $tracks->orderBy('created_at', 'desc')->get();

        $tracks->transform(function ($file) {
            $zips = ['zip', 'rar', '7z'];
            $vids = ['mp4', 'avi', 'mov', 'wmv', 'mkv'];
            $auds =  ['mp3', 'wav', 'ogg', 'm4a', 'flac'];
            $ext = pathinfo($file->original_file, PATHINFO_EXTENSION);
            $flag = array_search($ext,$zips) !== false ? 'pack' : (array_search($ext,$vids) !== false ? 'video' : (array_search($ext,$auds) !== false ? 'audio' : 'other'));
            return [
                'id' => (string) $file->id,
                'artist' => $file->user?->name ?? 'Desconocido',
                'title' => $file->name,
                'flag' => $flag,
                'cover' => $file->getPosterUrl() ?? $file->user->photo ?? config('app.logo_alter'),
                'bpm' => $file->bpm,
                'key' => $file->musical_note ?? '7A',
                'genre' => $file->categories->pluck('name')->toArray() ?? ['CubanPool'],
                'price' => $file->price,
                'url' => Storage::disk('s3')->url($file->file),
                'isNew' => Carbon::parse($file->created_at)->isCurrentDay(),
                'downloads' => $file->download_count,
                'canBeDownload' => $file->canBeDownload(),
                'downloadLink' => $file->canBeDownload() ? route('file.download', $file->id) : null,
                'addToCart' => route('file.add.cart', $file->id),
            ];
        });

        $artists = User::whereHas('files', function($q) use ($name){
            $q->audios()->where('status', 'active')
            ->whereHas('categories', function ($query) use ($name) {
                $query->where('name', $name);
            })
            ->whereJsonContains('sections', SectionEnum::MAIN->value);
        })->get();

        $genres = Category::whereHas('files', function($q) use ($name) {
            $q->audios()->where('status', 'active')
            ->whereHas('categories', function ($query) use ($name) {
                $query->where('name', $name);
            })
            ->whereJsonContains('sections', SectionEnum::MAIN->value);
        })->where('name','!=', $category->name)->orderBy('name')->get();
        
        $banners = [];

        if($category) 
        {
            $banners = [[ 'image' => $category->getCoverUrl()]];
        } else {
            $banners = [[ 'image' => asset('assets/img/hero-base.jpeg')]];
        }

        $index = 2;

        $metadata = [
            'title' => "$category->name",
            'description' => "Descarga los últimos remixes del género $category->name para DJs en alta calidad. Escucha un adelanto y llévalos directo a tu set.",
            'subtitle' => "Remixes de $category->name",
            'heroTooltip' => "Colección CubanPool",
            'heroTitle' => "Todo sobre",
            'heroAscent' => "$category->name",
            'heroDescription' => count($tracks). " remixes de $category->name para DJs en alta calidad. Escucha un adelanto y llévalos directo a tu set.",
            'heroLinkText' => null,
            'heroLinkAddress' => null,
        ];

        return view('music-catalog', compact('index', 'tracks', 'artists', 'genres', 'extras', 'banners', 'metadata'));
    }
}
