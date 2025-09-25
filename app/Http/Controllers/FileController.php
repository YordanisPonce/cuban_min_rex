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
}
