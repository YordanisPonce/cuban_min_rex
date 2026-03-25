<?php

namespace App\Filament\Resources\Files\Pages;

use App\Filament\Resources\Files\FileResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class EditFile extends EditRecord
{
    protected static string $resource = FileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $file = $this->record;
        if ($file->wasChanged('poster') && $file->poster) {
            $imagePath = Storage::disk('s3')->get($file->poster);
            $manager = new ImageManager(Driver::class);
            $image = $manager->read($imagePath)->scaleDown(width: 200, height: 200);
            $encoded = $image->encode(new WebpEncoder(quality: 50));
            $webpPath = 'images/'.Str::random().'.webp';
            $encoded->save(Storage::disk('public')->path($webpPath));

            $stream = fopen(Storage::disk('public')->path($webpPath), 'r');
                Storage::disk('s3')->writeStream($webpPath, $stream);
                if (is_resource($stream))
                    fclose($stream);

            $file->poster = $webpPath;
            $file->save();

            Storage::disk('public')->delete($webpPath);
            Storage::disk('s3')->delete($imagePath);
        }
    }
}
