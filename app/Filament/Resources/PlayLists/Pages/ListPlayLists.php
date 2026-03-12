<?php

namespace App\Filament\Resources\PlayLists\Pages;

use App\Filament\Resources\PlayLists\PlayListResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPlayLists extends ListRecords
{
    protected static string $resource = PlayListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Nueva PlayList'),
        ];
    }
}
