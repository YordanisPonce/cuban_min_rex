<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->record;

        if ($record->cover) {
            $imagePath = Storage::disk('public')->path($record->cover);
            $manager = new ImageManager(Driver::class);
            $image = $manager->read($imagePath);
            $encoded = $image->encode(new WebpEncoder(quality: 65));
            $webpPath = 'images/'.Str::random().'.webp';
            $encoded->save(Storage::disk('public')->path($webpPath));

            $stream = fopen(Storage::disk('public')->path($webpPath), 'r');
                Storage::disk('s3')->writeStream($webpPath, $stream);
                if (is_resource($stream))
                    fclose($stream);
            
            Storage::disk('public')->delete($webpPath);
            Storage::disk('public')->delete($imagePath);

            $record->update(['cover' => $webpPath]);
        }
    }
}
