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
            ->query(fn (): Builder => User::query()
                ->select('users.id', 'users.name', 'users.paypal_email')
                ->withCount([
                    'files as pending' => function ($query) {
                        $query->whereHas('sales', function ($query) {
                            $query->where('status', 'pending');
                        });
                    },
                    'files as paid_total' => function ($query) {
                        $query->whereHas('sales', function ($query) {
                            $query->where('status', 'paid');
                        });
                    },
                    'files as generated_total' => function ($query) {
                        $query->whereHas('sales', function ($query) {
                            $query->where('status', 'paid');
                        });
                    }
                ])
                ->selectRaw('
                    SUM(CASE WHEN sales.status = "pending" THEN sales.user_amount ELSE 0 END) as pending_amount,
                    SUM(CASE WHEN sales.status = "paid" THEN sales.user_amount ELSE 0 END) as total_paid,
                    SUM(CASE WHEN sales.status = "paid" THEN sales.admin_amount ELSE 0 END) as total_generated
                ')
                ->leftJoin('files', 'users.id', '=', 'files.user_id')
                ->leftJoin('sales', 'files.id', '=', 'sales.file_id')
                ->whereNot('role', 'user')
                ->groupBy('users.id', 'users.name', 'users.paypal_email')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre'),
                TextColumn::make('paypal_email')
                    ->label('Correo de PayPal'),
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
                /*Action::make('Pagar')
                    ->color('success')
                    ->icon('heroicon-m-currency-dollar')
                    ->disabled(fn($record) => !($record->pending_amount > 0))
                    ->action(function ($record) {
                        if (!$record->paypal_email) {
                            Notification::make()
                                ->title('Error: El usuario al que le quiere pagar no tiene definido un Correo de PayPal.')
                                ->danger()
                                ->send();
                        } else {
                            $paypal = new PaypalService();
                            try {
                                $response = $paypal->sendPayout($record->paypal_email,$record->pending_amount,'USD','Liquidación por ventas del mes '.Carbon::now()->month.' del año '.Carbon::now()->year.'.');

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
                                Sale::whereHas('file', function ($query) use ($id) {
                                    $query->where('user_id', $id);
                                })->update(['status' => 'paid']);

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
            ->heading('Liquidación de Ventas');
    }
}
