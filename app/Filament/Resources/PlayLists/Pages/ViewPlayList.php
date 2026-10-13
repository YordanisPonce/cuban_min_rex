<?php

namespace App\Filament\Resources\PlayLists\Pages;

use App\Filament\Resources\PlayLists\PlayListResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;

class ViewPlayList extends ViewRecord
{
    protected static string $resource = PlayListResource::class;

    protected ?string $heading = 'Detalles de la PlayList';

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
}
