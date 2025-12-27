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
            ->query(fn (): Builder => User::query()
                ->select('users.id', 'users.name', 'users.paypal_email')
                ->whereNot('role', 'user')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre'),
                TextColumn::make('paypal_email')
                    ->label('Correo de PayPal'),
                TextColumn::make('downloads_count')
                    ->label('Descargas')
                    ->numeric()
                    ->default(fn ($record) => $record->totalUnliquidatedDownloads()),
                TextColumn::make('pending_amount')
                    ->label('Pendiente a pago')
                    ->money()
                    ->sortable()
                    ->default(fn ($record) => $record->pendingSubscriptionLiquidation()),
                TextColumn::make('total_paid')
                    ->label('Total pagado')
                    ->money()
                    ->sortable()
                    ->default(fn ($record) => $record->paidSubscriptionLiquidation()),
                TextColumn::make('total_generated')
                    ->label('Total generado')
                    ->money()
                    ->sortable()
                    ->default(fn ($record) => $record->generatedToSubscriptionLiquidation()),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                /*Action::make('Pagar')
                    ->color('success')
                    ->icon('heroicon-m-currency-dollar')
                    ->disabled(fn($record) => !($record->pendingSubscriptionLiquidation() > 0))
                    ->action(function ($record) {
                        if (!$record->paypal_email) {
                            Notification::make()
                                ->title('Error: El usuario al que le quiere pagar no tiene definido un Correo de PayPal.')
                                ->danger()
                                ->send();
                        } else {
                            $paypal = new PaypalService();
                            try {
                                $response = $paypal->sendPayout($record->paypal_email,$record->pendingSubscriptionLiquidation(),'USD','Liquidación por subscripciones del mes '.Carbon::now()->month.' del año '.Carbon::now()->year.'.');

                                $payment = new Payment();
                                $payment->user_id = $record->id;
                                $payment->paypal_response = $response['paypal_response'];
                                $payment->item_id = $response['item_id'];
                                $payment->sender_batch_id = $response['sender_batch_id'];
                                $payment->amount = $response['amount'];
                                $payment->currency = $response['currency'];
                                $payment->email = $response['email'];
                                $payment->note = $response['note'];
                                $payment->save();

                                $id = $record->id;
                                Download::whereHas('file', function($query) use ($id) {
                                    $query->where('user_id', $id);
                                })
                                ->where('liquidated', false)
                                ->update(['liquidated' => true]);

                            } catch (\Throwable $th) {
                                Notification::make()
                                    ->title($th->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }
                    })
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-o-currency-dollar')
                    ->modalHeading('Confirmar Pago')
                    ->modalDescription('¿Estás seguro de que deseas proceder con el pago?')
                    ->modalSubmitActionLabel('Sí, proceder a pagar')
                    ->modalCancelActionLabel('No, cancelar'),
                */Action::make('Ver Pagos')
                    ->color('info')
                    ->icon('heroicon-m-eye')
                    ->url(fn($record) => route('user.payments', $record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ])
            ->heading('Liquidación por Subscripciones');;
    }
}
