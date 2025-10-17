<?php

namespace App\Filament\Resources\Files\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Response;

class FilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('bpm')
                    ->label('BPM')
                    ->searchable(),
                TextColumn::make('price')
                    ->label('Precio')
                    ->money()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Categoría'),
                TextColumn::make('collection.name')
                    ->label('Colección'),
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
                // Action::make('download')
                //     ->label('Descargar')
                //     ->hidden(fn($record) => Auth::user()->id != $record->user_id)
                //     ->icon(svg('entypo-download'))
                //     ->action(function ($record) {
                //         $path = storage_path('app/public/'.$record->file);
                //         return Response::download($path);
                //     }),
                EditAction::make()->hidden(fn($record) => Auth::user()->id != $record->user_id)->label('Editar'),
                DeleteAction::make()->hidden(fn($record) => Auth::user()->id != $record->user_id)->label('Eliminar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(
                fn(EloquentBuilder $query) => !auth()->user()->is_admin ? $query->where('user_id', Auth::user()->id): $query
            );
    }
}
