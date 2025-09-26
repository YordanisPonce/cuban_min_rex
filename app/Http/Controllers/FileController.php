<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Support\Facades\Response;

class FileController extends Controller
{
    public function download(string $id){

        $file = File::find($id);

        $path = storage_path('app/public/' . $file->file);

        if (!file_exists($path)) {
            abort(404);
        }

        return Response::download($path);
    }

    public function play(string $collectionId, string $id){
        $file = File::find($id);
        return response()->stream(function () use ($file) {
            echo file_get_contents(storage_path('app/public/'.$file->file));
        }, 200, [
            'Content-Type' => 'audio/mpeg',
            'Content-Disposition' => 'inline; filename="' . $file->name . '"',
        ]);
    }
}
