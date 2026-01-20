<?php

namespace App\Livewire;

use App\Models\Payment;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;

class UserPaymentsTable extends TableWidget
{
    /**
     * ✅ Este valor debe venir desde la Page (o desde query string si quieres).
     */
    public ?int $userId = null;

    /**
     * (Opcional) si tu page tiene un "record" y quieres seguir usando session por record.
     */
    public ?string $recordKey = null;

    public function mount(): void
    {
        // ✅ Si viene userId, lo persistimos (opcional) por recordKey para que no se pierda
        if ($this->recordKey) {
            $key = "current_user_id_" . auth()->id() . "_" . $this->recordKey;

            if ($this->userId) {
                session([$key => $this->userId]);
            } else {
                $this->userId = session($key);
            }
        }

        // ✅ Fallback final: si no vino nada, intenta cogerlo de la URL (?userId=)
        $this->userId ??= request()->integer('userId');
    }

    public function table(Table $table): Table
    {
        $userId = (int) $this->userId;

        return $table
            ->paginated(false)
            ->query(
                fn(): Builder => Payment::query()
                    ->where('user_id', $userId)
                    ->latest('created_at')
            )
            ->columns([
                TextColumn::make('created_at')->label('Creado')->since(),

                TextColumn::make('paid_at')->label('Pagado')->dateTime('Y-m-d H:i')->placeholder('—'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'succeeded' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('amount')->label('Monto')->money(),
                TextColumn::make('currency')->label('Moneda'),
                TextColumn::make('email')->label('Email pagado')->copyable(),

                TextColumn::make('paypal_response_id')
                    ->label('Payout ID')
                    ->copyable()
                    ->state(fn(Payment $record) => $record->paypal_response['batch_header']['payout_batch_id'] ?? 'N/A'),

                TextColumn::make('paypal_batch_status')
                    ->label('Batch status')
                    ->badge()
                    ->state(fn(Payment $record) => $record->paypal_response['batch_header']['batch_status'] ?? '—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('sender_batch_id')->label('Sender batch')->copyable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('item_id')->label('Item')->copyable()->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('error_message')->label('Error')->wrap()->limit(60)->placeholder('—')->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('note')->label('Descripción')->wrap()->limit(50),
            ])
            ->recordActions([
                // Ver detalle
                Action::make('view')
                    ->label('Ver')
                    ->icon('heroicon-m-eye')
                    ->color('gray')
                    ->modalHeading('Detalle del pago')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalContent(function (Payment $record) {
                        $payoutId = $record->paypal_response['batch_header']['payout_batch_id'] ?? 'N/A';
                        $batchStatus = $record->paypal_response['batch_header']['batch_status'] ?? '—';

                        return new \Illuminate\Support\HtmlString(
                            '<div style="display:grid;gap:10px;font-size:14px;">'
                            . '<div><strong>Estado:</strong> ' . e($record->status) . '</div>'
                            . '<div><strong>Monto:</strong> $' . number_format((float) $record->amount, 2) . ' ' . e($record->currency) . '</div>'
                            . '<div><strong>Email:</strong> ' . e($record->email) . '</div>'
                            . '<div><strong>Sender batch:</strong> <code>' . e($record->sender_batch_id ?? '—') . '</code></div>'
                            . '<div><strong>Item ID:</strong> <code>' . e($record->item_id ?? '—') . '</code></div>'
                            . '<div><strong>Payout Batch ID:</strong> <code>' . e($payoutId) . '</code></div>'
                            . '<div><strong>Batch status:</strong> ' . e($batchStatus) . '</div>'
                            . ($record->error_message ? '<div><strong>Error:</strong> <span style="color:#b91c1c;">' . e($record->error_message) . '</span></div>' : '')
                            . ($record->note ? '<div><strong>Nota:</strong> ' . e($record->note) . '</div>' : '')
                            . '</div>'
                        );
                    }),

                // Ver JSON
                Action::make('paypal_json')
                    ->label('PayPal JSON')
                    ->icon('heroicon-m-code-bracket-square')
                    ->color('gray')
                    ->modalHeading('Respuesta cruda de PayPal')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalContent(fn(Payment $record) => new \Illuminate\Support\HtmlString(
                        '<pre style="white-space:pre-wrap;font-size:12px;background:#0b1220;color:#e5e7eb;padding:12px;border-radius:12px;max-height:420px;overflow:auto;">'
                        . e(json_encode($record->paypal_response ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))
                        . '</pre>'
                    ))
                    ->visible(fn(Payment $record) => !empty($record->paypal_response)),
                Action::make('repay')
                    ->label('Reintentar pago')
                    ->icon('heroicon-m-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Reintentar payout en PayPal')
                    ->modalDescription('Se volverá a intentar enviar este payout a PayPal. Úsalo solo si el pago falló o quedó pendiente.')
                    ->visible(fn(Payment $record) => in_array($record->status, ['failed', 'pending'], true) && auth()->user()->isAdmin())
                    ->action(function (Payment $record) {

                        // Seguridad: solo permitir retry si tiene monto válido
                        if ((float) $record->amount <= 0) {
                            Notification::make()
                                ->title('Monto inválido para reintentar')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Email PayPal: si el payment no lo tiene, intenta con el del user
                        $email = $record->email ?: ($record->user?->paypal_email);

                        if (!$email) {
                            $record->update([
                                'status' => 'failed',
                                'error_message' => 'No hay correo PayPal definido para este pago (payment.email ni user.paypal_email).',
                            ]);

                            Notification::make()
                                ->title('No se puede reintentar: falta correo PayPal')
                                ->danger()
                                ->send();
                            return;
                        }

                        try {
                            // (Opcional) marcar como processing para evitar doble click
                            $record->update([
                                'status' => 'pending',
                                'error_message' => null,
                            ]);

                            /** @var \App\Services\PaypalService $paypal */
                            $paypal = app(\App\Services\PaypalService::class);

                            $resp = $paypal->sendPayout(
                                $email,
                                (float) $record->amount,
                                $record->currency ?: 'USD',
                                $record->note ?: 'Payment from your app'
                            );

                            // Tu service retorna exactamente estas keys:
                            // paypal_response, item_id, sender_batch_id, amount, currency, email, note
                            $record->update([
                                'status' => 'succeeded',
                                'paid_at' => now(),
                                'paypal_response' => $resp['paypal_response'] ?? null,
                                'item_id' => $resp['item_id'] ?? null,
                                'sender_batch_id' => $resp['sender_batch_id'] ?? null,
                                'amount' => (float) ($resp['amount'] ?? $record->amount),
                                'currency' => $resp['currency'] ?? ($record->currency ?: 'USD'),
                                'email' => $resp['email'] ?? $email,
                                'note' => $resp['note'] ?? $record->note,
                                'error_message' => null,
                            ]);

                            Notification::make()
                                ->title('Pago reenviado correctamente')
                                ->success()
                                ->send();

                        } catch (\Throwable $th) {

                            $record->update([
                                'status' => 'failed',
                                'error_message' => $th->getMessage(),
                            ]);

                            Notification::make()
                                ->title('Falló el reintento de pago')
                                ->body($th->getMessage())
                                ->danger()
                                ->send();
                        }
                    })->disabled(
                        fn(Payment $record) =>
                        !in_array($record->status, ['failed', 'pending'], true)
                        || !empty($record->paypal_response['batch_header']['payout_batch_id'] ?? null)
                    )
                ,

            ])

            /*    ->recordActions([

               ])
    */
            ->heading(
                fn() => $userId
                ? ('Pagos Realizados a ' . (User::find($userId)->name ?? 'Usuario'))
                : 'Pagos Realizados'
            );
    }
}
