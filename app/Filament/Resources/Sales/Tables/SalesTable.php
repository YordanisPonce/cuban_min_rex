<?php

namespace App\Filament\Resources\Sales\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('sales_count')
                    ->label('Total')
                    ->alignCenter()
                    ->default(fn($record) => $record->totalSalesCount()),
                TextColumn::make('pending_sales_count')
                    ->label('Pendiente')
                    ->alignCenter()
                    ->default(fn($record) => $record->pendingSalesCount()),
                TextColumn::make('sales_total')
                    ->label('Generado')
                    ->alignCenter()
                    ->numeric(2)
                    ->prefix('$ ')
                    ->default(fn($record) => $record->totalSalesAmount()),
                TextColumn::make('sales_pending')
                    ->label('Pendiente')
                    ->alignCenter()
                    ->numeric(2)
                    ->prefix('$ ')
                    ->default(fn($record) => $record->pendingSalesAmount()),
            ])
            ->filters([
                //
            ]);
    }
}
