<?php

namespace App\Livewire;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class DevSuscriptionTable extends TableWidget
{
    public ?string $month = null;

    public ?string $year = null;

    protected static ?string $heading = 'Suscripciones';

    public static function canView(): bool
    {
        return auth()->user()?->role === 'developer';
    }

    public function mount(?string $year = null, ?string $month = null): void
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->formatStateUsing(function($state){
                        Carbon::setLocale('es');
                        return Carbon::parse($state)->translatedFormat('j \d\e F \d\e Y');
                    }),
                TextColumn::make('user.name')
                    ->label('Cliente')
                    ->default('Anónimo'),
                TextColumn::make('plan.name')
                    ->label('Suscripción'),
                TextColumn::make('amount')
                    ->prefix('$ ')
                    ->label('Precio'),
                TextColumn::make('comision')
                    ->prefix('$ ')
                    ->label('Comisión')
                    ->default(fn($record) => $record->amount * 0.2),
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
                //
            ]);
    }

    public function getTableQuery(): Builder|Relation|null
    {
        $query = Order::query()->whereHas('plan')->where('status', 'paid');

        if ($this->month) {
            $query->whereMonth('created_at', $this->month);
        }

        if ($this->year) {
            $query->whereYear('created_at', $this->year);
        }

        return $query->orderBy('created_at', 'desc');
    }
}
