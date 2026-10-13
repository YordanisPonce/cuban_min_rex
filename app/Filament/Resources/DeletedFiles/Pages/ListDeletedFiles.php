<?php

namespace App\Filament\Resources\DeletedFiles\Pages;

use App\Filament\Resources\DeletedFiles\DeletedFilesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeletedFiles extends ListRecords
{
    protected static string $resource = DeletedFilesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //CreateAction::make(),
        ];
    }
}
