<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class OrderController extends Controller
{
    public function download(Request $request, string $id)
    {
        $token = $request->get('token');
        if ($token) {
            $user = User::whereJsonContains('downloadToken', $token)->first();
            $order = Order::find($id);
            if ($user && $order) {
                $downloadToken = $user->downloadToken;
                $indice = array_search($token, $downloadToken);
                unset($downloadToken[$indice]);
                $user->downloadToken = $downloadToken;
                $user->save();

                $zip = new ZipArchive();
                $zipFileName = ''.config('app.name').'-pack.zip';
                $zipFilePath = storage_path('app/public/files/zip' . $zipFileName);

                if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                    return response()->json(['error' => 'No se pudo crear el archivo ZIP'], 500);
                }

                foreach ($order->order_items as $key => $value) {
                    $file = File::find($value);
                    $path = $file->original_file;
                    if (!Storage::disk('s3')->exists($path)) {
                        abort(404);
                    }
                    $ext = pathinfo($path, PATHINFO_EXTENSION);
                    $downloadName = "$file->name.$ext";
                    $zip->addFile($path, $downloadName);
                }

                $zip->close();

                if (!file_exists($zipFilePath)) {
                    return response()->json(['error' => 'El archivo ' . $zipFileName . ' no se ha creado.'], 500);
                }
                
                return Response::download($zipFilePath)->deleteFileAfterSend(true);
            }
            return redirect('/')->with('error', 'Usted no tiene permisos para descargar el archivo seleccionado.');
        }
        return redirect('/')->with('error', 'Usted no tiene permisos para descargar el archivo seleccionado.');
    }
}
