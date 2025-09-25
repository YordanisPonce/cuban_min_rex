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
        $results = File::whereHas('collection', function ($query) use ($id) {
                $query->where('id', $id);
            })
            ->with(['collection'])
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

        return view('collection', compact('results', 'categories', 'collection'));
    }

    public function download(string $id){
        $collection = Collection::find($id);
        $zip = new ZipArchive();
        $zipFileName = ''.$collection->name.'.zip';
        $zipFilePath = storage_path('app/public/files/zip'.$zipFileName);

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            return response()->json(['error' => 'No se pudo crear el archivo ZIP'], 500);
        }

        $files = File::where('collection_id', $id)->get();

        $files = $files->filter(function ($item) {
            return pathinfo('storage/public/files/'.$item->file, PATHINFO_EXTENSION)!=='zip';
        });

        foreach ($files as $file) {
            $path = storage_path('app/public/'.$file->file);
            if (file_exists($path)) {
                $zip->addFile($path, basename($path));
            } else {
                return response()->json(['error' => 'El archivo '.$path.' no se ha encontrado.'], 500);
            }
        }

        $zip->close();

        if (!file_exists($zipFilePath)) {
            return response()->json(['error' => 'El archivo '.$zipFileName.' no se ha creado.'], 500);
        }
        
        return Response::download($zipFilePath)->deleteFileAfterSend(true);
    }
}
