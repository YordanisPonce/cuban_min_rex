<?php

namespace App\Observers;

use App\Http\Controllers\NotificationController;
use App\Models\PlayListItem;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PlaylistObserver
{
    /**
     * Handle the PlayListItem "created" event.
     */
    public function created(PlayListItem $playListItem): void
    {
        //
    }

    /**
     * Handle the PlayListItem "updated" event.
     */
    public function updated(PlayListItem $playListItem): void
    {
        //
    }

    /**
     * Handle the PlayListItem "deleted" event.
     */
    public function deleted(PlayListItem $playListItem): void
    {
        $dev = User::where('email', 'developer@cubanpool.com')->first();
        // Search the Sound 'file_path' into AWS and delete them
        Log::info('Deleting sound: '. $playListItem->title .' with ID: '. $playListItem->id);
        if ($playListItem->file_path) {
            $filePath = $playListItem->file_path;
            Log::info('Searching for sound in Storage: '. $filePath);
            if (Storage::disk('s3')->exists($filePath)) {
                Log::info('Sound found in Storage: '. $filePath);
                Log::info('Deleting sound from Storage: '. $filePath);
                Storage::disk('s3')->delete($filePath);
                NotificationController::sendSistemNtf($dev->id, 'Sound Eliminado', 'El archivo '.$playListItem->title.' ha sido eliminado de AWS S3');
            }
        }
    }

    /**
     * Handle the PlayListItem "restored" event.
     */
    public function restored(PlayListItem $playListItem): void
    {
        //
    }

    /**
     * Handle the PlayListItem "force deleted" event.
     */
    public function forceDeleted(PlayListItem $playListItem): void
    {
        //
    }
}
