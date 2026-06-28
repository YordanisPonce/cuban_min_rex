<?php

namespace App\Services;

use App\Models\PlayList;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipStream\CompressionMethod;
use ZipStream\ZipStream;

class PlaylistZipBuilder
{
    public function build(PlayList $playlist, string $localZipPath, ?callable $onProgress = null): int
    {
        $items = $playlist->items()->get();
        $tracksAdded = 0;

        $zipDirectory = dirname($localZipPath);

        if (!file_exists($zipDirectory)) {
            mkdir($zipDirectory, 0755, true);
        }

        $outputStream = fopen($localZipPath, 'wb');

        if (!$outputStream) {
            throw new \RuntimeException('No se pudo crear el archivo ZIP local.');
        }

        $zip = new ZipStream(
            outputStream: $outputStream,
            sendHttpHeaders: false,
            defaultCompressionMethod: CompressionMethod::STORE,
            enableZip64: true,
        );

        $usedNames = [];

        foreach ($items as $index => $item) {
            try {
                if (!Storage::disk('s3')->exists($item->file_path)) {
                    Log::error('Playlist ZIP: archivo no encontrado en S3: ' . $item->file_path);
                    continue;
                }

                $stream = Storage::disk('s3')->readStream($item->file_path);

                if (!$stream) {
                    Log::error('Playlist ZIP: no se pudo leer stream: ' . $item->file_path);
                    continue;
                }

                $extension = pathinfo($item->file_path, PATHINFO_EXTENSION);
                $safeTitle = preg_replace('/[^A-Za-z0-9_\- áéíóúÁÉÍÓÚñÑ]/u', '', $item->title) ?: 'track_' . $item->id;
                $fileNameInsideZip = $this->uniqueZipEntryName($safeTitle . '.' . $extension, $usedNames);

                Log::debug('Playlist ZIP: adding ' . ($index + 1) . '/' . $items->count() . ' - ' . $fileNameInsideZip);

                $zip->addFileFromStream(
                    fileName: $fileNameInsideZip,
                    stream: $stream,
                    compressionMethod: CompressionMethod::STORE,
                    exactSize: Storage::disk('s3')->size($item->file_path),
                );

                if (is_resource($stream)) {
                    fclose($stream);
                }

                $tracksAdded++;

                if ($onProgress) {
                    $onProgress($tracksAdded, $items->count());
                }
            } catch (\Throwable $e) {
                Log::error('Playlist ZIP: error en track ' . $item->file_path . ' - ' . $e->getMessage());

                if (isset($stream) && is_resource($stream)) {
                    fclose($stream);
                }
            }
        }

        $zip->addFile(
            fileName: 'copyright.txt',
            data: 'Esta playlist se ha descargado desde Cuban Pool',
            compressionMethod: CompressionMethod::STORE,
        );

        $zip->finish();
        fclose($outputStream);

        if ($tracksAdded === 0) {
            @unlink($localZipPath);
            throw new \RuntimeException('No se pudo agregar ningún archivo al ZIP.');
        }

        return $tracksAdded;
    }

    public function estimateTotalBytes(Collection $items): int
    {
        $total = 0;

        foreach ($items as $item) {
            if (!$item->file_path || !Storage::disk('s3')->exists($item->file_path)) {
                continue;
            }

            $total += Storage::disk('s3')->size($item->file_path);
        }

        return $total;
    }

    private function uniqueZipEntryName(string $name, array &$usedNames): string
    {
        if (!in_array($name, $usedNames, true)) {
            $usedNames[] = $name;

            return $name;
        }

        $pathInfo = pathinfo($name);
        $base = $pathInfo['filename'];
        $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
        $counter = 2;

        do {
            $candidate = $base . '_' . $counter . $extension;
            $counter++;
        } while (in_array($candidate, $usedNames, true));

        $usedNames[] = $candidate;

        return $candidate;
    }
}
