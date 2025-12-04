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
            $order = Order::with('order_items.file')->find($id);

            if ($user && $order) {
                $downloadToken = $user->downloadToken;
                $indice = array_search($token, $downloadToken);
                Log::debug('Download token index: ' . $indice);

                // Eliminar el token usado
                if ($indice !== false) {
                    unset($downloadToken[$indice]);
                    $user->downloadToken = array_values($downloadToken); // Reindexar array
                    $user->save();
                }

                $zip = new ZipArchive();
                $zipFileName = config('app.name') . '-' . uniqid() . '-pack.zip';
                $zipFilePath = Storage::disk('local')->path("files/zip/$zipFileName");

                // Asegurar que el directorio existe
                $zipDirectory = dirname($zipFilePath);
                if (!file_exists($zipDirectory)) {
                    mkdir($zipDirectory, 0755, true);
                }

                Log::debug('Creating ZIP file at: ' . $zipFilePath);

                if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                    return response()->json(['error' => 'No se pudo crear el archivo ZIP'], 500);
                }

                Log::debug('Adding files to ZIP archive');

                foreach ($order->order_items as $orderItem) {
                    // Verificar si existe el file_id
                    if (!$orderItem->file_id) {
                        continue;
                    }

                    $file = File::find($orderItem->file_id);

                    if (!$file) {
                        Log::warning('File not found with ID: ' . $orderItem->file_id);
                        continue;
                    }

                    Log::debug('Adding file to ZIP: ' . $file->original_file);

                    // Verificar si el archivo existe en S3
                    if (!Storage::disk('s3')->exists($file->original_file)) {
                        Log::warning('File not found in S3: ' . $file->original_file);
                        continue;
                    }

                    // Obtener el contenido del archivo desde S3
                    $fileContent = Storage::disk('s3')->get($file->original_file);

                    // Obtener la extensión del archivo
                    $ext = pathinfo($file->original_file, PATHINFO_EXTENSION);
                    $downloadName = $file->name . '.' . $ext;

                    // Agregar el archivo al ZIP desde el contenido en memoria
                    $zip->addFromString($downloadName, $fileContent);
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