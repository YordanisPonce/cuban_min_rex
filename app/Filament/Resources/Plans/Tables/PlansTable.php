<?php

namespace App\Filament\Resources\Plans\Tables;

// use Filament\Actions\BulkActionGroup;
// use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Filament\Actions\DeleteAction;

class PlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                // TextColumn::make('stripe_product_id')
                //     ->searchable(),
                // TextColumn::make('stripe_price_id')
                // ->searchable(),
                TextColumn::make('price')
                    ->label('Precio')
                    ->money()
                    ->sortable(),
                TextColumn::make('duration_months')
                    ->label('Meses de duración')
                    ->numeric()
                    ->sortable(),
                ImageColumn::make('image')
                    ->label('Image')
                    ->disk('public')
                    ->url(fn($record) => $record?->image),
                IconColumn::make('is_recommended')
                    ->label('Recomendado')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Fecha de Actualización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()->label('Editar'),
                DeleteAction::make()->label('Eliminar'),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
