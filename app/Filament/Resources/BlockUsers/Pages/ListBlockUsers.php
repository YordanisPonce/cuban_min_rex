<?php

namespace App\Filament\Resources\BlockUsers\Pages;

use App\Filament\Resources\BlockUsers\BlockUsersResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBlockUsers extends ListRecords
{
    protected static string $resource = BlockUsersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //CreateAction::make(),
        ];
    }
}
