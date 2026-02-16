<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SuscriptionComisionDetails extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $title = 'Detalles de Descargas';

    protected string $view = 'filament.pages.suscription-comision-details';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function canAccess(): bool
    {
        return true;
    }

    
    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('email')->label('Usuario'),
                TextColumn::make('downloads_all')->label('Descargas Totales')->default(fn($record) => $record->getDistinctDownloads())->alignCenter(),
                TextColumn::make('downloads_to')->label('Descargas Propias')->default(fn($record) => $record->getDistinctDownloadsTo(auth()->user()->id))->alignCenter(),
                TextColumn::make('percent')->label('Comisión')->default(fn($record) => round($record->getDistinctDownloadsTo(auth()->user()->id) / $record->getDistinctDownloads(), 2)*100 )->suffix('%')->alignCenter(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                //
            ])->defaultSort('created_at', 'desc')
            ->poll(null)
            ->heading('Descargas por Usuario')
            ->description('Descargas totales de los usuarios y las realizadas sobre tus archivos');
    }

    protected function getTableQuery()
    {
        $query = User::query()->where('role', 'user')->whereHas('downloads');

        return $query;
    }
}
