<?php

namespace App\Filament\Resources\DeletedFiles\Pages;

use App\Filament\Resources\DeletedFiles\DeletedFilesResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditDeletedFiles extends EditRecord
{
    protected static string $resource = DeletedFilesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
