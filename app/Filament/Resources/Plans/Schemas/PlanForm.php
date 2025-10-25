<?php

namespace App\Filament\Resources\Plans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use App\Models\Plan;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Illuminate\Support\Facades\Storage;

class PlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                // TextInput::make('stripe_product_id'),
                // TextInput::make('stripe_price_id'),
                TextInput::make('price')
                    ->label('Precio')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                RichEditor::make('description')
                    ->label('Descripción')
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->image()
                    ->columnSpanFull()
                    ->label('Subir Foto')
                    ->required()
                    ->disk('s3')
                    ->directory('images'),
                TextInput::make('duration_months')
                    ->label('Meses de Duración')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('downloads')
                    ->label('Descargas Mensuales')
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
                Repeater::make('features')->columnSpanFull()->reorderableWithButtons()->schema([
                    TextInput::make('value')
                        ->label('Característica')
                        ->required()->columnSpanFull()
                        ->maxLength(255),
                ])
            ]);
    }
}
