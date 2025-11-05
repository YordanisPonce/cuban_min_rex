<?php

namespace App\Types;

use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;

class CustomTemporaryUploadedFile extends TemporaryUploadedFile
{
    public static function generateHashNameWithOriginalNameEmbedded($file)
    {
        $hash = str()->random(30);
        $meta = str('-meta' . base64_encode(Str::random(10)) . '-')->replace('/', '_');
        $extension = '.' . $file->getClientOriginalExtension();

        return $hash . $meta . $extension;
    }

}