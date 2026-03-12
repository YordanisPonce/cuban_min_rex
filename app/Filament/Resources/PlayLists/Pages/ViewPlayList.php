<?php

namespace App\Filament\Resources\PlayLists\Pages;

use App\Filament\Resources\PlayLists\PlayListResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPlayList extends ViewRecord
{
    protected static string $resource = PlayListResource::class;

    protected ?string $heading = 'Detalles de la PlayList';
}
