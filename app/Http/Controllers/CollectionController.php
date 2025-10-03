<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use App\Models\File;
use Illuminate\Support\Facades\Response;
use ZipArchive;

class CollectionController extends Controller
{

    public function show(string $id)
    {
        $collection = Collection::find($id);
        $categories = Category::where('show_in_landing', true)->get();
        $recentCollections = Collection::orderBy('created_at', 'desc')->take(5)->get();
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get();
        $results = File::whereHas('collection', function ($query) use ($id) {
            $query->where('id', $id);
        })
            ->with(['collection'])
            ->get()
            ->map(function ($file) {
                $content = file_get_contents(storage_path('app/public/' . $file->file));
                $binaryContent = base64_encode($content);
                $isZip = pathinfo('storage/public/files/' . $file->file, PATHINFO_EXTENSION) !== 'mp3';

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
        $relationeds = Collection::where('category_id', $collection->category_id)->orWhere('user_id', $collection->user_id)->get();
        $relationeds = $relationeds->filter(function ($item) use ($collection) {
            return $item->id !== $collection->id && $item->files()->count() > 0;
        });

        return view('collection', compact('results', 'categories', 'collection', 'relationeds', 'recentCategories', 'recentCollections'));
    }

    public function download(string $id)
    {
        $collection = Collection::find($id);
        $zip = new ZipArchive();
        $zipFileName = '' . $collection->name . '.zip';
        $zipFilePath = storage_path('app/public/files/zip' . $zipFileName);

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            return response()->json(['error' => 'No se pudo crear el archivo ZIP'], 500);
        }

        $files = File::where('collection_id', $id)->get();

        $files = $files->filter(function ($item) {
            return pathinfo('storage/public/files/' . $item->file, PATHINFO_EXTENSION) !== 'zip';
        });

        foreach ($files as $file) {
            $path = storage_path('app/public/' . $file->file);
            if (file_exists($path)) {
                $zip->addFile($path, basename($path));
            } else {
                return response()->json(['error' => 'El archivo ' . $path . ' no se ha encontrado.'], 500);
            }
        }

        $zip->close();

        if (!file_exists($zipFilePath)) {
            return response()->json(['error' => 'El archivo ' . $zipFileName . ' no se ha creado.'], 500);
        }

        foreach ($files as $file) {
            $file->download_count = $file->download_count + 1;
            $file->save();
        }

        return Response::download($zipFilePath)->deleteFileAfterSend(true);
    }

    public function index()
    {
        $collections = Collection::All();
        $categories = Category::where('show_in_landing', true)->get();
        $recentCollections = Collection::orderBy('created_at', 'desc')->take(5)->get();
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get();

        $collections = $collections->filter(function ($item) {
            return $item->files()->count() > 0;
        });

        return view('category', compact('collections', 'categories', 'recentCategories', 'recentCollections'));
    }

    public function playlist(\App\Models\Collection $collection)
    {
        // Trae sus files; si tu lógica "hereda" categoría desde la colección, no hace falta tocar aquí
        $tracks = $collection->files()
            ->orderBy('id')
            ->get(['file as url', 'name as title'])
            ->map(fn($f) => [
                'url' => \Storage::disk('public')->url($f->url ?? $f->file), // adapta si ya guardas rutas absolutas
                'title' => $f->title ?? $f->name,
            ]);

        return response()->json($tracks);
    }

}
