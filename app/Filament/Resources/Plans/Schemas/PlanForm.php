<?php

namespace App\Filament\Resources\Plans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('stripe_product_id'),
                TextInput::make('stripe_price_id'),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('duration_months')
                    ->required()
                    ->numeric()
                    ->default(1),
                Toggle::make('is_recommended')
                    ->required(),
            ]);
    }
}
