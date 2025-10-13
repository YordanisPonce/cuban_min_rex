<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use App\Models\File;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $categories = Category::where('show_in_landing', true)->get();
        $recentCollections = Collection::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $word = $request->search;
        $results = File::where('name', 'like', '%' . $word . '%')
            ->orWhereHas('collection', function ($query) use ($word) {
                $query->where('name', 'like', '%' . $word . '%');
            })
            ->orWhereHas('category', function ($query) use ($word) {
                $query->where('name', 'like', '%' . $word . '%');
            })
            ->with(['collection', 'category']) // Carga las relaciones
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
}
