<?php

namespace App\Filament\Pages;

use App\Livewire\TabsWidget;
use App\Models\Download;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\User;
use App\Services\PaypalService;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class Liquidations extends Page
{
    protected string $view = 'filament.pages.liquidations';

    protected static ?string $title = 'Liquidaciones';

    protected static BackedEnum|string|null $navigationIcon = Heroicon::CurrencyDollar;

    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Procesar Pagos')
                ->color('success')
                ->icon('heroicon-m-currency-dollar')
                ->action(function () {
                    $users = User::whereNot('role', 'user')->get();
                    foreach ($users as $record) {
                        if (!$record->paypal_email) {
                            Notification::make()
                                ->title('Error: El usuario ' . $record->name . ' no tiene definido un Correo de PayPal.')
                                ->danger()
                                ->send();
                        } else {
                            $paypal = new PaypalService();
                            try {
                                $salesLiquidation = $record->pendingSaleLiquidation();
                                $subscriptionLiquidation = $record->pendingSubscriptionLiquidation();

                                $total = $salesLiquidation + $subscriptionLiquidation;
                                $response = $paypal->sendPayout($record->paypal_email, $total, 'USD', 'Liquidación por ventas - CubanPool ' . Carbon::now()->month . ' del año ' . Carbon::now()->year . '.');

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

                                Download::whereHas('file', function ($query) use ($id) {
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
                    }
                })
                ->requiresConfirmation()
                ->modalIcon('heroicon-o-currency-dollar')
                ->modalHeading('Confirmar Pago')
                ->modalDescription('¿Estás seguro de que deseas proceder con el pago?')
                ->modalSubmitActionLabel('Sí, proceder a pagar')
                ->modalCancelActionLabel('No, cancelar'),

        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TabsWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }
}
