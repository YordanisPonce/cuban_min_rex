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
                    'id' => $file->id,
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

    public function showCollections(string $id){
        $categories = Category::where('show_in_landing', true)->get();
        $category = Category::find($id);

        return view('category', compact('category', 'categories'));
    }
}
