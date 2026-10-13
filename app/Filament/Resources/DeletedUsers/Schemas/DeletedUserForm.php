<?php

namespace App\Filament\Resources\DeletedUsers\Schemas;

use Filament\Schemas\Schema;

class DeletedUserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }
}
