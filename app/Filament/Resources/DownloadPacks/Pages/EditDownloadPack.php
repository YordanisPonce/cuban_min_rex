<?php

namespace App\Filament\Resources\DownloadPacks\Pages;

use App\Filament\Resources\DownloadPacks\DownloadPackResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDownloadPack extends EditRecord
{
    protected static string $resource = DownloadPackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
