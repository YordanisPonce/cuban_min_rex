<?php

namespace App\Livewire;

use App\Models\File;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;

class UserEarningsTable extends TableWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => File::query()->where('user_id', auth()->user()->id))
            ->columns([
                TextColumn::make('name')->label('Archivo'),
                TextColumn::make('downloads')->label('Descargas')->default(fn($record) => count($record->downloads)),
                TextColumn::make('downloadsEarning')
                    ->label('Ganancias por Descargas')
                    ->default(fn($record) => $record->getDownloadsEarnings())
                    ->money(),
                TextColumn::make('sales')->label('Ventas Totales')->default(fn($record) => count($record->sales)),
                TextColumn::make('total')
                    ->label('Ganancias por Ventas')
                    ->default(fn($record) => $record->totalEarning())
                    ->money()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ])
            ->heading('Ganancia por venta de archivos');
    }
}
