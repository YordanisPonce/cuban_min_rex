<?php

namespace App\Http\Controllers;

use App\Enums\SectionEnum;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Download;
use App\Models\Collection;
use App\Models\File;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class CollectionController extends Controller
{

    public function index()
    {
        $dj = request()->get('dj');
        $name = request()->get('title');
    
        $packs = File::zips();

        if($dj){
            $packs = $packs->whereHas('user', function($q) use ($dj){
                $q->where('name', 'like', '%'.str_replace('_',' ', $dj).'%');
            });
        }

        if($name){
            $packs = $packs->where('name', 'like', '%'.$name.'%');
        }
        
        $packs = $packs->orderBy('download_count', 'desc')->paginate(10)->withQueryString();

        $packs->getCollection()->transform(function ($pack) {
            return [
                'id' => (string) $pack->id,
                'date' => $pack->created_at,
                'artist' => $pack->user->name,
                'title' => $pack->name,
                'img' => $pack->getPosterUrl() ?? $pack->user->photo ?? config('app.logo_alter'),
                'bpm' => $pack->bpm,
                'duration' => 120,
                'genre' => $pack->categories->pluck('name')->toArray() ?? ['DESCONOCIDO'],
                'folder' => $pack->folder?->name ?? '',
                'badge' => null,
                'price' => $pack->price,
                'url' => Storage::disk('s3')->url($pack->file),
                'downloads' => $pack->isExclusive ? $pack->sales->count() : $pack->download_count,
                'items_count' => null,
                'isNew' => Carbon::parse($pack->created_at)->isCurrentDay(),
                'canDownload' => $pack->isExclusive ? false : (auth()->check() && auth()->user()->hasActivePlan()),
                'isExclusive' => $pack->isExclusive,
                'downloadLink' => auth()->check() && auth()->user()->hasActivePlan() ? route('file.download', $pack->id) : null,
                'addToCart' => route('file.add.cart', $pack->id),
            ];
        });

        $djs = User::whereHas('files', fn($q) => $q->zips())->orderBy('name')->get();

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

        $index = 5;

        return view('packs', compact('index', 'djs', 'packs', 'banners'));
    }

    public function radio()
    {
        $collections = File::where('original_file','LIKE','%.zip')->whereJsonContains('sections', SectionEnum::CUBANDJS->value)->where('status','active')->orderBy('created_at', 'desc')->paginate(12);
        $djs = User::whereHas('files')->orderBy('name')->get();
        $categories = Category::where('show_in_landing', true)->orderBy('name')->get();

        $recentDjs = User::whereNot('role','user')->orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });

        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });

        $badge = 'Packs';
        $radio = 'true';

        return view('category', compact('radio','collections', 'djs', 'categories', 'recentCategories', 'recentDjs', 'badge'));
    }

}
