<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'file_id',
        'stripe_session_id',
        'stripe_payment_intent',
        'amount',
        'status',
        'paid_at',
        'expires_at'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function downloadFilesZip()
    {
        // Nombre del zip (sin / ni \)
        $zipFileName = 'order-' . $this->id . '-archivos.zip';

        // Ruta temporal local donde crear el zip
        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tempZipPath = $tempDir . DIRECTORY_SEPARATOR . $zipFileName;

        $zip = new ZipArchive();

        if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'No se pudo crear el archivo ZIP.');
        }

        // Recorremos los items de la orden
        $this->loadMissing('order_items.file');

        foreach ($this->order_items as $item) {
            $file = $item->file;

            if (!$file || !$file->path) {
                continue;
            }

            $s3Path = $file->path; // ej: "users/1/files/mi-archivo.pdf"

            if (!Storage::disk('s3')->exists($s3Path)) {
                continue;
            }

            // Nombre con el que irá dentro del ZIP
            $innerName = $file->original_name ?? basename($s3Path);

            // Aseguramos que no tenga / ni \
            $innerName = str_replace(['/', '\\'], '-', $innerName);

            // Leemos el contenido desde S3
            $stream = Storage::disk('s3')->readStream($s3Path);
            if (!$stream) {
                continue;
            }

            $contents = stream_get_contents($stream);
            fclose($stream);

            // Lo añadimos al ZIP
            $zip->addFromString($innerName, $contents);
        }

        $zip->close();

        // Devolvemos la respuesta de descarga y borramos el archivo después
        return response()->download($tempZipPath, $zipFileName)->deleteFileAfterSend(true);
    }
}
