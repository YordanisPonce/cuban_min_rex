<?php

namespace App\Filament\Resources\DeletedFiles\Schemas;

use Filament\Schemas\Schema;

class DeletedFilesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }
}
