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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['image']) {
            $imagePath = Storage::disk('public')->path($data['image']);
            $manager = new ImageManager(Driver::class);
            $image = $manager->read($imagePath);
            $encoded = $image->encode(new WebpEncoder(quality: 65));
            $webpPath = 'images/'.Str::random().'.webp';
            $encoded->save(Storage::disk('public')->path($webpPath));

            $stream = fopen(Storage::disk('public')->path($webpPath), 'r');
                Storage::disk('s3')->writeStream($webpPath, $stream);
                if (is_resource($stream))
                    fclose($stream);

            $data['image'] = $webpPath;
            
            Storage::disk('public')->delete($webpPath);
            Storage::disk('publick')->delete($imagePath);
        }
        return $data;
    }
}
