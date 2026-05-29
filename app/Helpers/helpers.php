<?php
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

if (!function_exists('downloadFileFromDisk')) {
    function downloadFileFromDisk(string $disk, string $path, string $downloadName): StreamedResponse
    {
        $storage = Storage::disk($disk);

        if (!$storage->exists($path)) {
            abort(404, 'Archivo no encontrado.');
        }

        $stream = $storage->readStream($path);

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type'        => $storage->mimeType($path) ?? 'application/octet-stream',
            'Content-Length'      => $storage->size($path),
            'Content-Disposition' => "attachment; filename=\"{$downloadName}\"; filename*=UTF-8''" . rawurlencode($downloadName),
        ]);
    }
}
