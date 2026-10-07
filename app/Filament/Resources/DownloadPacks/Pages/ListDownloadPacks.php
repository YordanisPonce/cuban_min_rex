<?php

namespace App\Filament\Resources\DownloadPacks\Pages;

use App\Filament\Resources\DownloadPacks\DownloadPackResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDownloadPacks extends ListRecords
{
    protected static string $resource = DownloadPackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->visible(fn() => auth()->user()->role != 'admin'),
        ];
    }
}
