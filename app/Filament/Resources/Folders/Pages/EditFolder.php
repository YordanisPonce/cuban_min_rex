<?php

namespace App\Filament\Resources\Folders\Pages;

use App\Filament\Resources\Folders\FolderResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditFolder extends EditRecord
{
    protected static string $resource = FolderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            Action::make('reset_cover')->label("Eliminar Cover")
                ->icon('heroicon-o-photo')->requiresConfirmation()->action(function () {
                    $record = $this->record;
                    try {
                        $imagePath = Storage::disk('s3')->get($record->cover_image);
                        Storage::disk('s3')->delete($imagePath);
                    } catch (\Exception $e) {
                        Notification::make()->body('No se pudo eliminar el poster anterior de AWS S3: ' . $e->getMessage())->warning()->send();
                    }
                    $record->update(['cover_image' => null]);
                    Notification::make()->body('Poster eliminado')->success()->send();
                })->visible(fn () => $this->record->cover_image !== null),
        ];
    }
}
