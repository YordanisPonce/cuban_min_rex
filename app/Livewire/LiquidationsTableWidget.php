<?php

namespace App\Livewire;

use App\Models\Payment;
use App\Models\Sale;
use App\Models\User;
use App\Services\PaypalService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LiquidationsTableWidget extends TableWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn(): Builder => User::query()->whereNot('role', 'user')
                    ->select('users.id', 'users.name', 'users.paypal_email')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre'),
                TextColumn::make('paypal_email')
                    ->label('Correo de PayPal'),
                TextColumn::make('pending_amount')
                    ->default(fn($record) => $record->pendingSalesTotal())
                    ->label('Pendiente a pago')
                    ->money()
                    ->sortable(),
                TextColumn::make('total_paid')
                    ->default(fn($record) => $record->paidSalesTotal())
                    ->label('Total pagado')
                    ->money()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                Action::make('Ver Pagos')
                    ->color('info')
                    ->icon('heroicon-m-eye')
                    ->url(fn($record) => route('user.payments', $record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ])
            ->heading('Liquidación de Ventas');
    }
}
