<?php

namespace App\Filament\Resources\Files\Pages;

use App\Filament\Resources\Files\FileResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
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
            Action::make('change_poster')->label('Cambiar Poster')
                ->icon('heroicon-o-photo')
                ->requiresConfirmation()
                ->schema([
                    \Filament\Forms\Components\FileUpload::make('poster')
                        ->label('Nuevo Poster')
                        ->image()
                        ->disk('public')
                        ->directory('images')
                        ->required(),
                ])
                ->action(function (array $data) {

                    try {
                        $file = $this->record;
                        if ($file->poster) {
                            try {
                                $oldPath = Storage::disk('s3')->get($file->poster);
                                Storage::disk('s3')->delete($oldPath);
                            } catch (\Exception $e) {
                                Notification::make()->body('No se pudo eliminar el poster anterior de AWS S3: ' . $e->getMessage())->warning()->persistent()->send();
                            }
                        }
                        if ($data['poster']) {
                            $imagePath = Storage::disk('public')->path($data['poster']);
                            $manager = new ImageManager(Driver::class);
                            $image = $manager->read($imagePath);
                            $encoded = $image->encode(new WebpEncoder(quality: 65));
                            $webpPath = 'images/' . Str::random() . '.webp';
                            $encoded->save(Storage::disk('public')->path($webpPath));

                            $stream = fopen(Storage::disk('public')->path($webpPath), 'r');
                            Storage::disk('s3')->writeStream($webpPath, $stream);
                            if (is_resource($stream))
                                fclose($stream);

                            Storage::disk('public')->delete($webpPath);
                            Storage::disk('public')->delete($imagePath);

                            $file->poster = $webpPath;
                            $file->save();
                            Notification::make()->body('Poster cambiado correctamente.')->success()->persistent()->send();
                        }
                    } catch (\Exception $e) {
                        Notification::make()->body('Error al cambiar el poster: ' . $e->getMessage())->danger()->persistent()->send();
                    }
                }),
            Action::make('change_preview')->label('Cambiar Preview/Audio')
                ->icon('heroicon-o-musical-note')
                ->requiresConfirmation()
                ->schema([
                    \Filament\Forms\Components\FileUpload::make('preview')
                        ->label('Subir vista previa del archivo')
                        ->acceptedFileTypes(['audio/*', 'video/*'])
                        ->disk('s3')
                        ->directory('files')
                        ->columnSpanFull(),
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label('Subir archivo original completo')
                        ->acceptedFileTypes(['audio/*', 'video/*'])
                        ->disk('s3')
                        ->directory('files')
                        ->columnSpanFull(),
                ])
                ->action(function (array $data) {
                    try {
                        $file = $this->record;
                        if ($data['preview']) {
                            if ($file->file) {
                                try {
                                    $filePath = Storage::disk('s3')->get($file->file);
                                    Storage::disk('s3')->delete($filePath);
                                } catch (\Exception $e) {
                                    Notification::make()->body('No se pudo eliminar el preview anterior de AWS S3: ' . $e->getMessage())->warning()->persistent()->send();
                                }
                            }
                            $file->file = $data['preview'];
                            $file->save();
                            Notification::make()->body('Preview cambiado correctamente.')->success()->persistent()->send();
                        }
                        if ($data['file']) {
                            if ($file->original_file) {
                                try {
                                    $filePath = Storage::disk('s3')->get($file->original_file);
                                    Storage::disk('s3')->delete($filePath);
                                } catch (\Exception $e) {
                                    Notification::make()->body('No se pudo eliminar el archivo original anterior de AWS S3: ' . $e->getMessage())->warning()->persistent()->send();
                                }
                            }
                            $file->original_file = $data['file'];
                            $file->save();
                            Notification::make()->body('Archivo original cambiado correctamente.')->success()->persistent()->send();
                        }
                    } catch (\Exception $e) {
                        Notification::make()->body('Error al proceder: ' . $e->getMessage())->danger()->persistent()->send();
                    }
                }),
        ];
    }
}
