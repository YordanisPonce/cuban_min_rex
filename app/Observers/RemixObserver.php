<?php

namespace App\Observers;

use App\Http\Controllers\NotificationController;
use App\Models\File;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RemixObserver
{
    /**
     * Handle the File "created" event.
     */
    public function created(File $file): void
    {
        //
    }

    /**
     * Handle the File "updated" event.
     */
    public function updated(File $file): void
    {
        //
    }

    /**
     * Handle the File "deleted" event.
     */
    public function deleted(File $file): void
    {
        $dev = User::where('email', 'developer@cubanpool.com')->first();
        // Search the File 'file' and 'original_file' into AWS and delete them
        Log::info('Deleting file: '. $file->name .' with ID: '. $file->id);
        if ($file->file) {
            $filePath = $file->file;
            Log::info('Searching for file in Storage: '. $filePath);
            if (Storage::disk('s3')->exists($filePath)) {
                Log::info('File found in Storage: '. $filePath);
                Log::info('Deleting file from Storage: '. $filePath);
                Storage::disk('s3')->delete($filePath);
                NotificationController::sendSistemNtf($dev->id, 'Archivo Eliminado', 'El archivo '.$file->name.' ha sido eliminado de AWS S3');
            }
        }
        if ($file->original_file) {
            $originalFilePath = $file->original_file;
            Log::info('Searching for original file in Storage: '. $originalFilePath);
            if (Storage::disk('s3')->exists($originalFilePath)) {
                Log::info('Original file found in Storage: '. $originalFilePath);
                Log::info('Deleting original file from Storage: '. $originalFilePath);
                Storage::disk('s3')->delete($originalFilePath);
                NotificationController::sendSistemNtf($dev->id, 'Archivo Original Eliminado', 'El archivo original '.$file->name.' ha sido eliminado de AWS S3');
            }
        }
    }

    /**
     * Handle the File "restored" event.
     */
    public function restored(File $file): void
    {
        //
    }

    /**
     * Handle the File "force deleted" event.
     */
    public function forceDeleted(File $file): void
    {
        //
    }
}
