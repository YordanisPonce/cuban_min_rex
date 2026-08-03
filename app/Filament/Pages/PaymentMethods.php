<?php

namespace App\Filament\Pages;

use App\Models\AviablePaymentMethod;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use UnitEnum;

class PaymentMethods extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected string $view = 'filament.pages.payment-methods';
    protected static ?string $slug = 'payment-methods';
    Protected static ?string $navigationLabel = 'Métodos de Pago';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';
    protected static ?int $navigationSort = 9999;
    protected static string|UnitEnum|null $navigationGroup = 'Configuraciones';
    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role == 'admin';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->role == 'admin' ?? false;
    }

    public function mount(): void
    {
        $payments = AviablePaymentMethod::firstOrCreate([]);
        $this->form->fill([
            'stripe' => $payments->stripe,
            'paypal' => $payments->paypal,
        ]);
    }
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Tabs::make('payment_methods')
                    ->tabs([
                        Tabs\Tab::make('apm')
                            ->label('Métodos de Pago')
                            ->schema([
                                Toggle::make('stripe')
                                    ->onIcon('heroicon-s-check-circle')
                                    ->offIcon('heroicon-s-x-circle')
                                    ->label('Pago con Tarjeta (Stripe)')
                                    ->helperText('Habilitar o deshabilitar Stripe como método de pago.'),
                                Toggle::make('paypal')
                                    ->onIcon('heroicon-s-check-circle')
                                    ->offIcon('heroicon-s-x-circle')
                                    ->label('Pago con PayPal')
                                    ->helperText('Habilitar o deshabilitar PayPal como método de pago.'),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $payments = AviablePaymentMethod::firstOrCreate([]);
        $payments->update([
            'stripe' => $data['stripe'] ?? null,
            'paypal' => $data['paypal'] ?? null,
        ]);

        Notification::make()
            ->title('Configuración guardada')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Guardar Configuración')
                ->action('save'),
        ];
    }
}
