<?php

namespace App\Filament\Resources\DeletedUsers\Pages;

use App\Filament\Resources\DeletedUsers\DeletedUserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeletedUsers extends ListRecords
{
    protected static string $resource = DeletedUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //CreateAction::make(),
        ];
    }
}
