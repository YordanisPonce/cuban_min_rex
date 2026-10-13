<?php

namespace App\Filament\Resources\DeletedFiles\Pages;

use App\Filament\Resources\DeletedFiles\DeletedFilesResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDeletedFiles extends CreateRecord
{
    protected static string $resource = DeletedFilesResource::class;
}
