<?php

namespace App\Filament\Resources\Carts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CartForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->numeric()
                    ->default(null),
                Textarea::make('uuid')
                    ->label('UUID')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('items')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
