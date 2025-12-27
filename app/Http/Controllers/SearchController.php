<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $categories = Category::where('show_in_landing', true)->orderBy('name')->get();
        $djs = User::whereHas('files')->orderBy('name')->get();
        $recentDjs = User::whereNot('role', 'user')->orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });

        $name = request()->get("search") ?? "";
        $category = request()->get("categories") ?? "";
        $remixers = request()->get("remixers") ?? "";

        $results = File::whereJsonContains('sections', SectionEnum::MAIN->value)
            ->where('status', 'active')
            ->where('name', 'like', '%' . $name . '%')
            ->whereHas('category', function ($query) use ($category) {
                $query->where('name', 'like', '%' . $category . '%');
            })
            ->whereHas('user', function ($query) use ($remixers) {
                $query->where('name', 'like', '%' . $remixers . '%');
            })
            ->with(['user', 'category']) // Carga las relaciones
            ->orderBy('created_at', 'desc')
            ->paginate(30)->withQueryString();

        $playList = File::whereJsonContains('sections', SectionEnum::MAIN->value)
            ->where('status', 'active')
            ->where('name', 'like', '%' . $name . '%')
            ->whereHas('category', function ($query) use ($category) {
                $query->where('name', 'like', '%' . $category . '%');
            })
            ->whereHas('user', function ($query) use ($remixers) {
                $query->where('name', 'like', '%' . $remixers . '%');
            })
            ->with(['user', 'category']) // Carga las relaciones
            ->orderBy('created_at', 'desc')
            ->get()->map(fn($f) => [
                'id' => $f->id,
                'url' => Storage::disk('s3')->url($f->url ?? $f->file), // adapta si ya guardas rutas absolutas
                'title' => $f->title ?? $f->name,
            ]);

        $results->getCollection()->transform(function ($file) {
            $isZip = pathinfo(Storage::disk('s3')->url($file->url ?? $file->original_file), PATHINFO_EXTENSION) === 'zip';
            return [
                'id' => $file->id,
                'date' => $file->created_at,
                'user' => $file->user->name,
                'name' => $file->name,
                'logotipe' => $file->user->photo,
                'bpm' => $file->bpm,
                'collection' => $file->collection ? $file->collection->name : null,
                'category' => $file->category ? $file->category->name : null,
                'price' => $file->price,
                'url' => route('file.play', [$file->collection ? $file->collection->id : 'none', $file->id]),
                'isZip' => $isZip,
                'ext' => pathinfo(Storage::disk('s3')->url($file->url ?? $file->file), PATHINFO_EXTENSION),
            ];
        });

        $allCategories = Category::orderBy('name')->get();
        $allRemixers = User::whereHas('files', function ($query) {
            $query->where('name', 'like', '%%');
        })->get();


        return view('search', compact('results', 'djs', 'categories', 'recentCategories', 'recentDjs', 'allCategories', 'allRemixers', 'playList'));
    }
}
