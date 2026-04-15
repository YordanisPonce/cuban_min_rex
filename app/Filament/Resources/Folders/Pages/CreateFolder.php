<?php

namespace App\Filament\Resources\Folders\Pages;

use App\Filament\Resources\Folders\FolderResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateFolder extends CreateRecord
{
    protected static string $resource = FolderResource::class;

    public function getHeading(): string|Htmlable
    {
        return 'Crear Carpeta';
    }
}
