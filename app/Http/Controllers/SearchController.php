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
                    'name' => $file->name,
                    'collection' => $file->collection->name ?? null,
                    'category' => $file->collection->category->name ?? null,
                    'price' => $file->price,
                ];
            });

        return view('search', compact('results'));
    }
}
