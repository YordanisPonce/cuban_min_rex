<?php

namespace App\Filament\Resources\BlockUsers\Pages;

use App\Filament\Resources\BlockUsers\BlockUsersResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBlockUsers extends EditRecord
{
    protected static string $resource = BlockUsersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
