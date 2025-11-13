<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Category;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function show($categoryId)
    {
        $categories = Category::where('show_in_landing', true)->orderBy('name')->get();
        $djs = User::whereHas('files')->orderBy('name')->get();
        $recentCollections = Collection::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });

        $name = request()->get("search") ?? "";
        $remixers = request()->get("remixers") ?? "";

        $results = File::where('name', 'like', '%' . $name . '%')
            ->whereHas('category', function ($query) use ($categoryId) {
                $query->where('id', $categoryId);
            })
            ->whereHas('user', function ($query) use ($remixers) {
                $query->where('name', 'like', '%' . $remixers . '%');
            })
            ->with(['user', 'category']) // Carga las relaciones
            ->orderBy('created_at', 'desc')
            ->paginate(30);
        
        $playList = File::where('name', 'like', '%' . $name . '%')
            ->whereHas('category', function ($query) use ($categoryId) {
                $query->where('id', $categoryId);
            })
            ->whereHas('user', function ($query) use ($remixers) {
                $query->where('name', 'like', '%' . $remixers . '%');
            })
            ->with(['user', 'category']) // Carga las relaciones
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($f) => [
                'id' => $f->id,
                'url' => Storage::disk('s3')->url($f->url ?? $f->file), // adapta si ya guardas rutas absolutas
                'title' => $f->title ?? $f->name,
            ]);

        $results->getCollection()->transform(function ($file) {
            $isZip = pathinfo(Storage::disk('s3')->url($file->url ?? $file->file), PATHINFO_EXTENSION) === 'zip';
            return [
                'id' => $file->id,
                'date' => $file->created_at,
                'user' => $file->user->name,
                'name' => $file->name,
                'bpm' => $file->bpm,
                'collection' => $file->collection->name ?? null,
                'category' => $file->category->name ?? null,
                'price' => $file->price,
                'url' => route('file.play', [$file->collection ? $file->collection->id : 'none', $file->id]),
                'isZip' => $isZip
            ];
        });

        $category = Category::find($categoryId);

        $allCategories = Category::orderBy('name')->get();
        $allRemixers = User::whereHas('files', function ($query) use ($categoryId) {
            $query->where('category_id', $categoryId);
        })->get();

        return view('search', compact('category','results', 'djs','categories', 'recentCategories', 'recentCollections', 'allCategories', 'allRemixers', 'playList'));
    }

    public function showCollections(string $id)
    {
        $categories = Category::where('show_in_landing', true)->orderBy('name')->get();
        $recentCollections = Collection::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $ctgName = Category::find($id)->name;
        $collections = Category::find($id)->collections()->paginate(12);

        return view('category', compact('ctgName', 'collections', 'categories', 'recentCategories', 'recentCollections'));
    }
}
