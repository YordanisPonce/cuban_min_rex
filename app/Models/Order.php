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
        'expires_at',
        'settled_at', // ✅ añadir

        //pagos CUP
        'currency',
        'phone',
        'code',
        'customer_email',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
        'settled_at' => 'datetime', // ✅ añadir
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

        // Ruta RELATIVA dentro de storage/app
        $relativeZipPath = 'temp/' . $zipFileName;

        // Disk local (storage/app)
        $localDisk = Storage::disk('local');

        // Ruta absoluta donde se creará el ZIP
        $absoluteZipPath = $localDisk->path($relativeZipPath);

        // Aseguramos que el directorio exista
        $dir = dirname($absoluteZipPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $zip = new ZipArchive();

        if ($zip->open($absoluteZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'No se pudo crear el archivo ZIP.');
        }

        // Cargamos los items con sus files
        $this->loadMissing('order_items.file');

        $filesAdded = 0;

        foreach ($this->order_items as $item) {
            $file = $item->file;

            if (!$file || !$file->original_file) {
                continue;
            }

            $s3Path = $file->original_file; // ej: "users/1/files/mi-archivo.pdf"

            if (!Storage::disk('s3')->exists($s3Path)) {
                continue;
            }

            // Nombre dentro del ZIP
            $innerName = $file->name ?? basename($s3Path);
            $ext = pathinfo($file->original_file, PATHINFO_EXTENSION);
            $innerName = str_replace(['/', '\\'], '-', $innerName);
            $innerName = $innerName . '.' . $ext;

            // Leemos desde S3 vía stream
            $stream = Storage::disk('s3')->readStream($s3Path);
            if (!$stream) {
                continue;
            }

            $contents = stream_get_contents($stream);
            fclose($stream);

            // Añadimos al ZIP
            $zip->addFromString($innerName, $contents);
            $filesAdded++;
        }

        $zip->close();

        // Si no se añadió ningún archivo o el zip no existe, no intentamos descargar
        if ($filesAdded === 0 || !$localDisk->exists($relativeZipPath)) {
            if ($localDisk->exists($relativeZipPath)) {
                $localDisk->delete($relativeZipPath);
            }

            abort(404, 'Esta orden no tiene archivos descargables.');
        }

        // Descargamos usando Storage y borramos después de enviar
        return $localDisk
            ->download($relativeZipPath, $zipFileName);
    }

}
