<?php

namespace App\Filament\Resources\DownloadPacks\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DownloadPackForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('extra_downloads')
                    ->label('Descargas Extra')
                    ->numeric()
                    ->default(0)
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->label('Precio')
                    ->numeric()
                    ->prefix('$ ')
                    ->default(4.99)
                    ->columnSpanFull(),
                Toggle::make('active')
                    ->label('Activar')
                    ->default(true)
                    ->columnSpanFull(),
            ]);
    }
}
