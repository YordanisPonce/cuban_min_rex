<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('dj_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('rating')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                Textarea::make('comment')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
