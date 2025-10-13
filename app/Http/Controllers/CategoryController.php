<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Category;
use App\Models\Collection;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show($categoryId)
    {
        $categories = Category::where('show_in_landing', true)->get();
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
            ->paginate(10);

        $results->getCollection()->transform(function ($file) {
            $isZip = pathinfo('storage/public/files/'.$file->file, PATHINFO_EXTENSION) === 'zip';
            return [
                'id' => $file->id,
                'date' => $file->created_at,
                'user' => $file->user->name,
                'name' => $file->name,
                'collection' => $file->collection->name ?? null,
                'category' => $file->collection->category->name ?? null,
                'price' => $file->price,
                'url' => route('file.play', [$file->collection ? $file->collection->id : 'none', $file->id]),
                'isZip' => $isZip
            ];
        });


        return view('search', compact('results', 'categories', 'recentCategories', 'recentCollections'));
    }

    public function showCollections(string $id)
    {
        $categories = Category::where('show_in_landing', true)->get();
        $recentCollections = Collection::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $category = Category::find($id);

        return view('category', compact('category', 'categories', 'recentCategories', 'recentCollections'));
    }
}
