<?php

namespace App\Filament\Resources\PlayLists\Pages;

use App\Filament\Resources\PlayLists\PlayListResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class EditPlayList extends EditRecord
{
    protected static string $resource = PlayListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['cover']) {
            $imagePath = Storage::disk('public')->path($data['cover']);
            $manager = new ImageManager(Driver::class);
            $image = $manager->read($imagePath);
            $encoded = $image->encode(new WebpEncoder(quality: 65));
            $webpPath = 'playlists/covers/'.Str::random().'.webp';
            $encoded->save(Storage::disk('public')->path($webpPath));

            $stream = fopen(Storage::disk('public')->path($webpPath), 'r');
                Storage::disk('s3')->writeStream($webpPath, $stream);
                if (is_resource($stream))
                    fclose($stream);

            $data['cover'] = $webpPath;
            
            Storage::disk('public')->delete($webpPath);
            Storage::disk('public')->delete($imagePath);
        }
        return $data;
    }
}
