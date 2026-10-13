<?php

namespace App\Filament\Resources\DeletedUsers\Pages;

use App\Filament\Resources\DeletedUsers\DeletedUserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDeletedUser extends CreateRecord
{
    protected static string $resource = DeletedUserResource::class;
}
