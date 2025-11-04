<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                IconColumn::make('show_in_landing')
                    ->label('Mostrar en Home')
                    ->boolean()
                    ->visible(fn() => Auth::user()->role == 'admin'),
                TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Fecha de actualización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()->hidden(fn($record) => Auth::user()->id != $record?->user_id)->label('Editar'),
                DeleteAction::make()->hidden(fn($record) => Auth::user()->id != $record?->user_id)->label('Eliminar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->hidden(fn($record) => Auth::user()->id != $record?->user_id)->label('Eliminar marcados'),
                ]),
            ])->modifyQueryUsing(
                fn(EloquentBuilder $query) => !auth()->user()->is_admin ? $query->where('is_general', true)->orWhere('user_id', Auth::user()->id)->orderBy('name') : $query->orderBy('name')
            );
    }
}
