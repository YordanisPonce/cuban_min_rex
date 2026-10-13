<?php

namespace App\Filament\Resources\PlayLists\Pages;

use App\Filament\Resources\PlayLists\PlayListResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
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
            Action::make('change_poster')->label('Cambiar Poster')
                ->icon('heroicon-o-photo')
                ->requiresConfirmation()
                ->schema([
                    \Filament\Forms\Components\FileUpload::make('poster')
                        ->label('Nuevo Poster')
                        ->image()
                        ->required()
                        ->disk('s3')
                        ->directory('playlists/covers')
                        ->visibility('public'),
                ])
                ->action(function (array $data) {
                    try {
                        $file = $this->record;
                        if ($file->cover) {
                            try {
                                $imagePath = Storage::disk('s3')->get($file->cover);
                                Storage::disk('s3')->delete($imagePath);
                            } catch (\Exception $e) {
                                Notification::make()->body('No se pudo eliminar el poster anterior de AWS S3: ' . $e->getMessage())->warning()->persistent()->send();
                            }
                        }
                        $file->cover = $data['poster'];
                        $file->save();
                        Notification::make()->body('Poster cambiado correctamente.')->success()->persistent()->send();
                    } catch (\Exception $e) {
                        Notification::make()->body('Error al cambiar el poster: ' . $e->getMessage())->danger()->persistent()->send();
                    }
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['cover']) {
            $imagePath = Storage::disk('public')->path($data['cover']);
            $manager = new ImageManager(Driver::class);
            $image = $manager->read($imagePath);
            $encoded = $image->encode(new WebpEncoder(quality: 65));
            $webpPath = 'playlists/covers/' . Str::random() . '.webp';
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
