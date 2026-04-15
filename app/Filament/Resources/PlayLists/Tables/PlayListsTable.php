<?php

namespace App\Filament\Resources\PlayLists\Tables;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PlayListsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('items_count')
                    ->label('Archivos')
                    ->default(fn ($record) => $record->items()->count())
                    ->alignCenter(),
                TextColumn::make('folder.name')
                    ->label('Carpeta')
                    ->default(fn ($record) => $record->folder?->name ?? 'Sin carpeta')
                    ->searchable(),
                TextColumn::make('price')
                    ->label('Precio')
                    ->prefix('$ '),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label('Ver'),
                EditAction::make()->label('Editar'),
                DeleteAction::make()->label('Eliminar'),
            ])
            ->toolbarActions([
                //
            ])
            ->modifyQueryUsing(fn ($query) => auth()->user()->role !== 'admin' ? $query->where('user_id', auth()->id()) : $query)
            ->emptyStateHeading('No se han encontrado PlayLists')
            ->emptyStateDescription('Crea tu primera PlayList para empezar a organizar tus archivos de audio.');
    }
}
