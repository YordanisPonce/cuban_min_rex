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
        $word = $request->search;
        $results = File::where('name', 'like', '%' . $word . '%')
            ->orWhereHas('collection', function ($query) use ($word) {
                $query->where('name', 'like', '%' . $word . '%');
            })
            ->orWhereHas('collection.category', function ($query) use ($word) {
                $query->where('name', 'like', '%' . $word . '%');
            })
            ->with(['collection', 'collection.category']) // Carga las relaciones
            ->get()
            ->map(function ($file) {
                return [
                    'date' => $file->created_at,
                    'user' => $file->user->name,
                    'name' => $file->name,
                    'collection' => $file->collection->name ?? null,
                    'category' => $file->collection->category->name ?? null,
                    'price' => $file->price,
                    'url' => $file->file,
                ];
            });
        $results = $results->filter(function ($item) {
            return pathinfo('storage/public/files/'.$item['url'], PATHINFO_EXTENSION)!=='zip';
        });

        return view('search', compact('results', 'categories'));
    }
}
