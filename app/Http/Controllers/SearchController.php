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
            ->orWhereHas('collection.category', function ($query) use ($word) {
                $query->where('name', 'like', '%' . $word . '%');
            })
            ->with(['collection', 'collection.category']) // Carga las relaciones
            ->paginate(10);

        $results->getCollection()->transform(function ($file) {
            $content = file_get_contents(storage_path('app/public/' . $file->file)); 
            $binaryContent = base64_encode($content);
            $isZip = pathinfo('storage/public/files/'.$file->file, PATHINFO_EXTENSION) !== 'mp3';

            return [
                'id' => $file->id,
                'date' => $file->created_at,
                'user' => $file->user->name,
                'name' => $file->name,
                'collection' => $file->collection->name ?? null,
                'category' => $file->collection->category->name ?? null,
                'price' => $file->price,
                'url' => $binaryContent,
                'isZip' => $isZip
            ];
        });

        return view('search', compact('results', 'categories', 'recentCategories', 'recentCollections'));
    }
}
