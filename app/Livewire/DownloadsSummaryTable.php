<?php

namespace App\Livewire;

use App\Models;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Enums\PaginationMode;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class DownloadsSummaryTable extends TableWidget
{
    
    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('file.id')->label('Id del archivo')->searchable(),
                Tables\Columns\TextColumn::make('file.name')->label('Nombre del archivo')->searchable(),
                Tables\Columns\TextColumn::make('files.categories')
                    ->label('Categorías')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        return $record->file->categories->pluck('name');
                    })
                    ->default('Sin categoría'),
                Tables\Columns\TextColumn::make('user.email')->label('Cliente')->default('Usuario Anónimo'),
                Tables\Columns\TextColumn::make('created_at')->label('Fecha de venta')->formatStateUsing(function($state) {
                    Carbon::setLocale('es');
                    return Carbon::parse($state)->translatedFormat('j \d\e F \d\e Y');
                }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('file.categories')
                    ->label('Categoría')
                    ->relationship('file.categories', 'name')
                    ->optionsLimit(100)
                    ->multiple(),
            ])
            ->recordActions([
                //
            ])
            ->defaultSort('created_at', 'desc')
            ->poll(null)
            ->heading('Descargas realiazadas')
            ->description('Aquí puedes ver un resumen de las descargas realizadas a tus archivos.')
            ->emptyStateHeading('No se han realizado descargas')
            ->emptyStateDescription('Aún no se han realizado descargas a tus archivos.')
            ->searchPlaceholder('Buscar por ID o Nombre')
            ->defaultPaginationPageOption('10')
            ->paginationMode(PaginationMode::Default)
            ->modifyQueryUsing(
                fn($query) => auth()->user()->role!=='admin' ? $query->whereHas('file', function($q) { $q->where('user_id', auth()->user()->id);}) : $query
            );
    }

    protected function getTableQuery(): Builder|Relation|null
    {
        $query = Models\Download::query();

        return $query->orderBy('created_at', 'desc');
    }
}
