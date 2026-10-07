<?php

namespace App\Filament\Resources\DownloadPacks\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DownloadPacksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('extra_downloads')
                    ->label('Descargas Extra')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Precio')
                    ->money()
                    ->sortable(),
                IconColumn::make('active')
                    ->label('Activado')
                    ->alignCenter()
                    ->icons(fn(string $state) => match ($state) {
                        '1' => ['heroicon-o-check-circle'],
                        '0' => ['heroicon-o-x-circle'],
                        default => ucfirst($state),
                    })
                    ->colors(fn(string $state) => match ($state) {
                        '1' => ['success'],
                        '0' => ['danger'],
                        default => ['primary'],
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('setActive')
                    ->label(fn($record) => $record->active ? 'Desactivar' : 'Activar')
                    ->requiresConfirmation()
                    ->modalHeading(fn($record) => $record->active ? 'Desactivar pack' : 'Activar pack')
                    ->modalDescription(fn($record) => $record->active ? '¿Estás seguro de que deseas desactivar este pack?' : '¿Estás seguro de que deseas activar este pack?')
                    ->action(function ($record) {
                        $record->active = !$record->active;
                        $record->save();
                    })
                    ->icon(fn($record) => $record->active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn($record) => $record->active ? 'danger' : 'success')
                    ->hidden(fn($record) => auth()->user()->role != 'admin'),
            ])
            ->toolbarActions([
                //
            ]);
    }
}
