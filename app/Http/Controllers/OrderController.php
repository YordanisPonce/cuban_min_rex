<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class OrderController extends Controller
{
    public function download(Request $request, string $id)
    {
        $token = $request->get('token');
        Log::debug('Download token: ' . $token);
        if ($token) {

            $user = User::whereJsonContains('downloadToken', $token)->first();
            $order = Order::find($id);
            if ($user && $order) {
                $downloadToken = $user->downloadToken;
                $indice = array_search($token, $downloadToken);
                Log::debug('Download token index: ' . $indice);
                //unset($downloadToken[$indice]);
                $user->downloadToken = $downloadToken;
                $user->save();

                $zip = new ZipArchive();
                $zipFileName = config('app.name') . '-' . uniqid() . '-pack.zip';
                $zipFilePath = Storage::path("files/zip/$zipFileName");
                Log::debug('Creating ZIP file at: ' . $zipFilePath);

                if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                    return response()->json(['error' => 'No se pudo crear el archivo ZIP'], 500);
                }

                Log::debug('Adding files to ZIP archive');
                foreach ($order->order_items as $key => $value) {
                    $file = File::find($value->file_id);
                    Log::debug('Adding file to ZIP: ' . $file->original_file);
                    $path = Storage::disk('s3')->path($file->original_file);
                    if (!Storage::disk('s3')->exists($file->original_file)) {
                        // abort(404);
                        continue;
                    }
                    $ext = pathinfo($path, PATHINFO_EXTENSION);
                    $downloadName = "$file->name.$ext";
                    $zip->addFile(Storage::disk('s3')->path($file->original_file), $downloadName);
                }
                Log::debug('Closing ZIP archive');

                $zip->close();

                if (!file_exists($zipFilePath)) {
                    return response()->json(['error' => 'El archivo ' . $zipFileName . ' no se ha creado.'], 500);
                }
                Log::debug('ZIP file created successfully at: ' . $zipFilePath);

                return Response::download($zipFilePath)->deleteFileAfterSend(true);
            }
            return redirect('/')->with('error', 'Usted no tiene permisos para descargar el archivo seleccionado.');
        }
        return redirect('/')->with('error', 'Usted no tiene permisos para descargar el archivo seleccionado.');
    }
}
