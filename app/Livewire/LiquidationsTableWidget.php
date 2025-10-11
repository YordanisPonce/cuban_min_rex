<?php

namespace App\Livewire;

use App\Models\Payment;
use App\Models\User;
use App\Services\PaypalService;
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
            ->query(fn (): Builder => User::query()
                ->select('users.id', 'users.name', 'users.paypal_email')
                ->withCount([
                    'sales as pending' => function ($query) {
                        $query->where('status', 'pending');
                    },
                    'sales as paid_total' => function ($query) {
                        $query->where('status', 'paid');
                    },
                    'sales as generated_total' => function ($query) {
                        $query->where('status', 'paid');
                    }
                ])
                ->selectRaw('
                    SUM(CASE WHEN sales.status = "pending" THEN sales.user_amount ELSE 0 END) as pending_amount,
                    SUM(CASE WHEN sales.status = "paid" THEN sales.user_amount ELSE 0 END) as total_paid,
                    SUM(CASE WHEN sales.status = "paid" THEN sales.admin_amount ELSE 0 END) as total_generated
                ')
                ->leftJoin('sales', 'users.id', '=', 'sales.user_id')
                ->groupBy('users.id', 'users.name', 'users.paypal_email')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('paypal_email')
                    ->label('Corre de PayPal')
                    ->searchable(),
                TextColumn::make('pending_amount')
                    ->label('Pendiente a pago')
                    ->money()
                    ->sortable(),
                TextColumn::make('total_paid')
                    ->label('Total pagado')
                    ->money()
                    ->sortable(),
                TextColumn::make('total_generated')
                    ->label('Total generado')
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
                Action::make('Pagar')
                    ->color('success')
                    ->icon('heroicon-m-currency-dollar')
                    ->action(function ($record) {
                        if (!$record->paypal_email) {
                            Notification::make()
                                ->title('Error: El usuario al que le quiere pagar no tiene definido un Correo de PayPal.')
                                ->danger()
                                ->send();
                        } else {
                            $paypal = new PaypalService();
                            try {
                                $response = $paypal->sendPayout($record->paypal_email,$record->pending_amount);

                                $payment = new Payment();
                                $payment->user_id = $record->id;
                                $payment->response_json = $response['response_json'];
                                $payment->item_id = $response['item_id'];
                                $payment->sender_batch_id = $response['sender_batch_id'];
                                $payment->amount = $response['amount'];
                                $payment->currency = $response['currency'];
                                $payment->email = $response['email'];
                                $payment->note = $response['note'];
                                $payment->save();

                                User::find($record->id)->sales()->where('status', 'pending')->update(['status' => 'paid']);

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
                Action::make('Ver')
                    ->color('info')
                    ->icon('heroicon-m-eye'),
                Action::make('Ver Pagos')
                    ->color('success')
                    ->icon('heroicon-m-eye')
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ])
            ->heading(null);
    }
}
