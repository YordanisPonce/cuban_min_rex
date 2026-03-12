<?php

namespace App\Filament\Resources\PlayLists\Pages;

use App\Filament\Resources\PlayLists\PlayListResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPlayList extends EditRecord
{
    protected static string $resource = PlayListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
