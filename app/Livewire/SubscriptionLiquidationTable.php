<?php

namespace App\Livewire;

use App\Models\Download;
use App\Models\Payment;
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

class SubscriptionLiquidationTable extends TableWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn(): Builder => User::query()
                    ->select('users.id', 'users.name', 'users.paypal_email')
                    ->whereNot('role', 'user')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre'),
                TextColumn::make('paypal_email')
                    ->label('Correo de PayPal'),
                TextColumn::make('downloads_count')
                    ->label('Descargas por liquidar')
                    ->numeric()
                    ->default(fn($record) => $record->totalUnliquidatedDownloads()),
                TextColumn::make('pending_amount')
                    ->label('Pendiente a pago')
                    ->money()
                    ->sortable()
                    ->default(fn($record) => $record->pendingSubscriptionLiquidation()),
                TextColumn::make('total_paid')
                    ->label('Total pagado')
                    ->money()
                    ->sortable()
                    ->default(fn($record) => $record->paidSubscriptionLiquidation()),
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
            ->heading('Liquidación por Subscripciones');
        ;
    }
}
