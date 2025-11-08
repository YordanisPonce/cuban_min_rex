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
        $djs = User::where('role', 'worker')->orderBy('name')->get();
        $recentCollections = Collection::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        
        $results = File::where(function ($query) use ($categoryId) {
                $query->whereHas('collection.category', function ($q) use ($categoryId) {
                    $q->where('id', $categoryId);
                })->orWhereHas('category', function ($q) use ($categoryId) {
                    $q->where('categories.id', $categoryId);
                });
            })
            ->with(['collection.category'])
            ->orderBy('created_at', 'desc')
            ->paginate(30);
        
        $name = request()->get("search");
        $category = request()->get("categories");
        $remixers = request()->get("remixers");
        if ($name || $category || $remixers) {
            $results = File::where('name', 'like', '%' . $name . '%')
                ->whereHas('category', function ($query) use ($category) {
                    $query->where('name', 'like', '%' . $category . '%');
                })
                ->whereHas('user', function ($query) use ($remixers) {
                    $query->where('name', 'like', '%' . $remixers . '%');
                })
                ->with(['user', 'category']) // Carga las relaciones
                ->orderBy('created_at', 'desc')
                ->paginate(30);
        }

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
        
        $playList = []; 

        foreach ($results as $f) {
            $file = File::find($f['id']);
            $track = $file->get()
                ->map(fn($f) => [
                    'id' => $f->id,
                    'url' => Storage::disk('s3')->url($f->url ?? $f->file), // adapta si ya guardas rutas absolutas
                    'title' => $f->title ?? $f->name,
                ]);
            array_push($playList, $track);
        }


        $allCategories = Category::all();
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
