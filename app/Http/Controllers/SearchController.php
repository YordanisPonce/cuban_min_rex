<?php

namespace App\Http\Controllers;

use App\Enums\SectionEnum;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Collection;
use App\Models\File;
use App\Models\PlayList;
use App\Models\PlayListItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $search = $request->get('name') ?? "";

        $words = explode(' ', $search);

        $data = [];

        $remixes = File::audios()->section(SectionEnum::MAIN->value)->search($words, true)->get()->transform( function($e) {
            return[
                'name' => 'REMIX: '.$e->name,
                'artist' => $e->user?->name ?? 'Desconocido',
                'img' => $e->getPosterUrl() ?? $e->user?->photo ?? config('app.logo'),
                'dj_logo' => $e->user?->photo ?? config('app.logo'),
                'url' => route('remixes', ['title' => $e->name]),
            ];
        });

        foreach ($remixes as $r) {
            $data[] = $r;
        }

        $videos = File::videos()->section(SectionEnum::MAIN->value)->search($words, true)->get()->transform( function($e) {
            return[
                'name' => 'VIDEO: '.$e->name,
                'artist' => $e->user->name,
                'img' => $e->getPosterUrl() ?? $e->user->photo ?? config('app.logo'),
                'dj_logo' => $e->user->photo ?? config('app.logo'),
                'url' => route('videos', ['title' => $e->name]),
            ];
        });

        foreach ($videos as $v) {
            $data[] = $v;
        }

        $packs = File::zips()->section(SectionEnum::MAIN->value)->search($words, true)->get()->transform( function($e) {
            return[
                'name' => 'PACK: '.$e->name,
                'artist' => $e->user->name,
                'img' => $e->getPosterUrl() ?? $e->user->photo ?? config('app.logo'),
                'dj_logo' => $e->user->photo ?? config('app.logo'),
                'url' => route('collection.index', ['title' => $e->name]),
            ];
        });

        foreach ($packs as $p) {
            $data[] = $p;
        }

        $playlist = PlayList::search($words, true)->get()->transform( function($e) {
            return[
                'name' => 'PLAYLIST: '.$e->name,
                'artist' => $e->user->name,
                'img' => $e->getCoverUrl() ?? $e->user->photo ?? config('app.logo'),
                'dj_logo' => $e->user->photo ?? config('app.logo'),
                'url' => route('playlist.list', ['title' => $e->name]),
            ];
        });

        foreach ($playlist as $p) {
            $data[] = $p;
        }

        $song = PlayListItem::search($words, true)->get()->transform( function($e) {
            return[
                'name' => 'TRACK: '.$e->title.' en PLAYLIST: '.$e->playList->name,
                'artist' => $e->playList->user->name,
                'img' => $e->playList->getCoverUrl() ?? $e->playList->user->photo ?? config('app.logo'),
                'dj_logo' => $e->playList->user->photo ?? config('app.logo'),
                'url' => route('playlist.show', $e->playList->name),
            ];
        });

        foreach ($song as $s) {
            $data[] = $s;
        }

        $collection = collect($data);

        $perPage = 10; 
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $items = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $results = new LengthAwarePaginator(
            $items,
            $collection->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

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

        $index = 999;

        return view('search', compact('results', 'index', 'banners'));
    }
}
