<?php

namespace App\Livewire;

use App\Models\Payment;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;

class UserPaymentsTable extends TableWidget
{
    protected int $userId;

    public function mount(int $userId)
    {
        $this->userId = $userId;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Payment::where('user_id', $this->userId)->orderBy('created_at', 'desc'))
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha'),
                TextColumn::make('amount')
                    ->label('Cantidad Pagada')
                    ->money()
                    ->sortable(),
                TextColumn::make("paypal_responce['batch_header']['payout_batch_id']")
                    ->label('Transacción')
                    ->default('No definido'),
                TextColumn::make('note')
                    ->label('Descripción'),
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
            ->heading(fn() => 'Pagos Realizados a '. User::find($this->userId)->name);
    }
}
