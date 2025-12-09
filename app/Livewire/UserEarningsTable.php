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
                TextColumn::make('sales')->label('Ventas este Mes')->default(fn($record) => count($record->sales()->whereMonth('created_at', Carbon::now()->month)->get())),
                TextColumn::make('monthly')
                    ->label('Ganado este mes')
                    ->default(fn($record) => $record->monthlyEarning())
                    ->money()
                    ->sortable(),
                TextColumn::make('total_sales')->label('Ventas Totales')->default(fn($record) => count($record->sales)),
                TextColumn::make('total')
                    ->label('Total Ganado')
                    ->default(fn($record) => $record->totalEarning())
                    ->money()
                    ->sortable()
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
