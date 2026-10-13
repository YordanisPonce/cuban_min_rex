<?php

namespace App\Filament\Resources\DeletedUsers\Pages;

use App\Filament\Resources\DeletedUsers\DeletedUserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditDeletedUser extends EditRecord
{
    protected static string $resource = DeletedUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
