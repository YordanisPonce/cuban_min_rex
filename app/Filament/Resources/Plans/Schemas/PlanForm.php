<?php

namespace App\Filament\Resources\Plans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use App\Models\Plan;

class PlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                // TextInput::make('stripe_product_id'),
                // TextInput::make('stripe_price_id'),
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
                    ->required()
                    ->label('Recomendado')
                    ->reactive()
                    ->rules([
                        function ($attribute, $value, $fail) {
                            if ($value) {
                                $existing = Plan::where('is_recommended', true)->first();
                                if ($existing) {
                                    $fail('Ya existe un plan recomendado. Desmarca el otro primero.');
                                }
                            }
                        },
                    ]),
                FileUpload::make('image')
                    ->image()
                    ->avatar()
                    ->label('Subir Foto')
                    ->required()
                    ->disk('public')
                    ->directory('images') 
                    ->preserveFilenames(),
            ]);
    }
}
