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
                'id' => "f$e->id",
                'name' => 'REMIX: '.$e->name,
                'artist' => $e->user?->name ?? 'Desconocido',
                'price' => $e->price,
                'img' => $e->getPosterUrl() ?? $e->user?->photo ?? config('app.logo'),
                'dj_logo' => $e->user?->photo ?? config('app.logo'),
                'intro' => $e->intro(),
                'url' => $e->isExclusive ? route('exclusives', ['title' => $e->name]) : route('remixes', ['title' => $e->name]),
                'action' => true,
                'canDownload' => $e->canBeDownload(),
                'download' => $e->canBeDownload() ? route('file.download', $e->id) : null,
                'buy' => route('file.add.cart', $e->id),
            ];
        });

        foreach ($remixes as $r) {
            $data[] = $r;
        }

        $videos = File::videos()->section(SectionEnum::MAIN->value)->search($words, true)->get()->transform( function($e) {
            return[
                'id' => "f$e->id",
                'name' => 'VIDEO: '.$e->name,
                'artist' => $e->user?->name,
                'img' => $e->getPosterUrl() ?? $e->user->photo ?? config('app.logo'),
                'price' => $e->price,
                'dj_logo' => $e->user->photo ?? config('app.logo'),
                'intro' => $e->intro(),
                'url' => $e->isExclusive ? route('exclusives', ['title' => $e->name]) : route('videos', ['title' => $e->name]),
                'action' => true,
                'canDownload' => $e->canBeDownload(),
                'download' => $e->canBeDownload() ? route('file.download', $e->id) : null,
                'buy' => route('file.add.cart', $e->id),
            ];
        });

        foreach ($videos as $v) {
            $data[] = $v;
        }

        $packs = File::zips()->section(SectionEnum::MAIN->value)->search($words, true)->get()->transform( function($e) {
            return[
                'id' => "f$e->id",
                'name' => 'PACK: '.$e->name,
                'artist' => $e->user->name,
                'img' => $e->getPosterUrl() ?? $e->user->photo ?? config('app.logo'),
                'price' => $e->price,
                'dj_logo' => $e->user->photo ?? config('app.logo'),
                'intro' => $e->intro(),
                'url' => $e->isExclusive ? route('exclusives', ['title' => $e->name]) : route('collection.index', ['title' => $e->name]),
                'action' => true,
                'canDownload' => $e->canBeDownload(),
                'download' => $e->canBeDownload() ? route('file.download', $e->id) : null,
                'buy' => route('file.add.cart', $e->id),
            ];
        });

        foreach ($packs as $p) {
            $data[] = $p;
        }

        $playlist = PlayList::search($words, true)->get()->transform( function($e) {
            return[
                'id' => "p$e->id",
                'name' => 'PLAYLIST: '.$e->name,
                'artist' => $e->user->name,
                'img' => $e->getCoverUrl() ?? $e->user->photo ?? config('app.logo'),
                'price' => $e->price,
                'dj_logo' => $e->user->photo ?? config('app.logo'),
                'intro' => null,
                'url' => route('playlist.show', str_replace(' ', '_', $e->name)),
                'action' => false,
                'canDownload' => $e->canBeDownload(),
            ];
        });

        foreach ($playlist as $p) {
            $data[] = $p;
        }

        $song = PlayListItem::search($words, true)->get()->transform( function($e) {
            return[
                'id' => "pi$e->id",
                'name' => 'TRACK: '.$e->title.' en PLAYLIST: '.$e->playList->name,
                'artist' => $e->playList->user->name,
                'img' => $e->playList->getCoverUrl() ?? $e->playList->user->photo ?? config('app.logo'),
                'price' => $e->price,
                'dj_logo' => $e->playList->user->photo ?? config('app.logo'),
                'intro' => $e->intro(),
                'url' => route('playlist.show', str_replace(' ', '_', $e->playList->name)),
                'action' => true,
                'canDownload' => $e->playlist->canBeDownload(),
                'download' => $e->playlist->canBeDownload() ? route('playlist.download_item', [str_replace(' ', '_', $e->playlist->name), $e->id]) : null,
                'buy' => route('playlist.add.item.cart', [str_replace(' ', '_', $e->playlist->name), $e->id]),
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

        $bns = Banner::where('active', true)->get();
        $banners = [];

        if($bns->count() > 0) 
        {
            foreach ($bns as $bn) {
                $banner = [ 'image' => Storage::disk('s3')->url($bn->path ?? ''), 'isVideo' => $bn->isVideo ];
                array_push($banners,$banner);
            }

        } else {
            $banners = [[ 'image' => asset('assets/img/hero-base.jpeg')]];
        }

        $index = 999;

        return view('search', compact('results', 'index', 'banners'));
    }
}
