<?php

namespace App\Filament\Resources\DeletedFiles\Tables;

use App\Models\Category;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class DeletedFilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->onlyTrashed()->orderBy('deleted_at', 'desc'))
            ->columns([
                TextColumn::make('name')->label('Nombre'),
                TextColumn::make('user.name')->label('Artista'),
                TextColumn::make('bpm')
                    ->label('BPM')
                    ->searchable(),
                TextColumn::make('musical_note')
                    ->label('Nota')
                    ->default('-')
                    ->alignCenter()
                    ->searchable(),
                TextColumn::make('categories')
                    ->label('Categorías')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        return $record->categories->pluck('name');
                    })
                    ->default('Sin categoría'),
                TextColumn::make('deleted_at')->label('Fecha de Eliminado')->formatStateUsing(fn($state) => Carbon::parse($state)->translatedFormat('d \d\e F \d\e Y')),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->relationship('user', 'name', function($query){
                        $query->whereHas('files');
                    })
                    ->label('DJ')
                    ->hidden(fn() => auth()->user()->role!=='admin' && auth()->user()->role!=='developer'),
                SelectFilter::make('categories')
                    ->relationship(name: 'categories', titleAttribute: 'name')
                    ->label('Categorías')
                    ->options(fn (): array => Category::orderBy('name')->pluck('name', 'id')->all())
                    ->multiple(),
            ])
            ->recordActions([
                //EditAction::make(),
                RestoreAction::make()->visible(auth()->user()->role === 'admin'),
                ForceDeleteAction::make()->label('Eliminar permanentemente')->visible(auth()->user()->role === 'admin'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ForceDeleteBulkAction::make()->label('Eliminar permanentemente'),
                    RestoreBulkAction::make(),
                ])->visible(auth()->user()->role === 'admin'),
            ]);
    }
}
