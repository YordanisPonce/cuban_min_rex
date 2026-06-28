<?php

namespace App\Jobs;

use App\Models\Download;
use App\Models\PlayList;
use App\Models\PlaylistZipRequest;
use App\Models\User;
use App\Services\PlaylistZipBuilder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BuildPlaylistZipJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 7200;

    public int $tries = 1;

    public function __construct(public PlaylistZipRequest $zipRequest)
    {
    }

    public function handle(PlaylistZipBuilder $builder): void
    {
        $this->zipRequest->refresh();

        if ($this->zipRequest->status !== 'pending') {
            return;
        }

        $this->zipRequest->update(['status' => 'processing']);

        $playlist = PlayList::with('items')->find($this->zipRequest->play_list_id);

        if (!$playlist) {
            $this->failRequest('La playlist ya no existe.');

            return;
        }

        $localZipPath = Storage::disk('local')->path('files/zip/' . $this->zipRequest->uuid . '.zip');

        try {
            Log::debug('Playlist ZIP job: creating archive at ' . $localZipPath);

            $tracksAdded = $builder->build(
                $playlist,
                $localZipPath,
                function (int $added, int $total): void {
                    $this->zipRequest->update([
                        'tracks_added' => $added,
                        'tracks_total' => $total,
                    ]);
                }
            );

            if (!file_exists($localZipPath)) {
                throw new \RuntimeException('El archivo ZIP no se generó correctamente.');
            }

            $s3ZipPath = 'files/zip/temp/' . $this->zipRequest->uuid . '.zip';

            Log::debug('Playlist ZIP job: uploading to S3 ' . $s3ZipPath);

            $handle = fopen($localZipPath, 'r');
            Storage::disk('s3')->put($s3ZipPath, $handle);

            if (is_resource($handle)) {
                fclose($handle);
            }

            @unlink($localZipPath);

            $this->registerDownloadIfNeeded();

            $this->zipRequest->update([
                'status' => 'ready',
                's3_path' => $s3ZipPath,
                'tracks_added' => $tracksAdded,
                'tracks_total' => $playlist->items()->count(),
                'expires_at' => now()->addHours(24),
            ]);

            Log::debug('Playlist ZIP job: ready ' . $this->zipRequest->uuid);
        } catch (\Throwable $e) {
            Log::error('Playlist ZIP job failed: ' . $e->getMessage(), [
                'uuid' => $this->zipRequest->uuid,
                'playlist_id' => $this->zipRequest->play_list_id,
            ]);

            @unlink($localZipPath);
            $this->failRequest($e->getMessage());
        }
    }

    private function registerDownloadIfNeeded(): void
    {
        if ($this->zipRequest->download_registered) {
            return;
        }

        $user = User::find($this->zipRequest->user_id);

        if (!$user || $user->role === 'admin') {
            $this->zipRequest->update(['download_registered' => true]);

            return;
        }

        $download = new Download();
        $download->user_id = $user->id;
        $download->play_list_id = $this->zipRequest->play_list_id;
        $download->amount = $user->downloads_cost();
        $download->user_amount = $user->downloads_cost() * 0.7;
        $download->admin_amount = $user->downloads_cost() * 0.1;
        $download->save();

        $this->zipRequest->update(['download_registered' => true]);
    }

    private function failRequest(string $message): void
    {
        $this->zipRequest->update([
            'status' => 'failed',
            'error_message' => $message,
        ]);
    }
}
