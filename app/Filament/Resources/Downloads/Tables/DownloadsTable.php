<?php

namespace App\Filament\Resources\Downloads\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DownloadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('downloads_count')
                    ->label('Total')
                    ->alignCenter()
                    ->default(fn($record) => $record->totalDownloadsCount()),
                TextColumn::make('pending_downloads_count')
                    ->label('Pendiente')
                    ->alignCenter()
                    ->default(fn($record) => $record->pendingDownloadsCount()),
                TextColumn::make('downloads_total')
                    ->label('Generado')
                    ->alignCenter()
                    ->numeric(2)
                    ->prefix('$ ')
                    ->default(fn($record) => $record->totalDownloadsAmount()),
                TextColumn::make('downloads_pending')
                    ->label('Pendiente')
                    ->alignCenter()
                    ->numeric(2)
                    ->prefix('$ ')
                    ->default(fn($record) => $record->pendingDownloadsAmount()),
            ])
            ->filters([
                //
            ]);
    }
}
