<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show($categoryId) {
        $categories = Category::where('show_in_landing', true)->get();
        $results = File::whereHas('collection.category', function ($query) use ($categoryId) {
                $query->where('id', $categoryId);
            })
            ->with(['collection.category'])
            ->get()
            ->map(function ($file) {
                return [
                    'name' => $file->name,
                    'collection' => $file->collection->name ?? null,
                    'category' => $file->collection->category->name ?? null,
                    'price' => $file->price,
                ];
            });
            
        return view('search', compact('results', 'categories'));
    }
}
