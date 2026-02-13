<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\FilePaid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Notification as MailNotification;
use Filament\Actions\Action;
use Filament\Schemas\Components\Tabs;
use BackedEnum;
use Carbon\Carbon;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class Settings extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected string $view = 'filament.pages.settings';

    protected static ?string $navigationLabel = 'Pagos en CUP';

    protected static ?string $title = 'Pagos en CUP';

    protected static ?int $navigationSort = 9999;

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
        $setting = Setting::firstOrCreate([]);
        $this->form->fill([
            'credit_card_info' => $setting->credit_card_info,
            'confirmation_phone' => $setting->confirmation_phone,
            'confirmation_email' => $setting->confirmation_email,
            'currency_convertion_rate' => $setting->currency_convertion_rate,
            /*'eltoque_api_token' => $setting->eltoque_api_token,*/
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        Tabs\Tab::make('Datos para los Pagos')
                            ->icon('heroicon-o-credit-card')
                            ->schema([
                                TextInput::make('credit_card_info')
                                    ->label('Tarjeta de Crédito para Pagos en CUP')
                                    ->helperText('A esta tarjeta se le enviaran los pagos que se realicen en CUP.')
                                    ->prefix('💳')
                                    ->required()
                                    ->placeholder('XXXX-XXXX-XXXX-XXXX'),
                                TextInput::make('confirmation_phone')
                                    ->label('Número de Teléfono para Confirmaciones')
                                    ->helperText('A este número se le enviaran las confirmaciones de los pagos realizados en CUP.')
                                    ->prefix('📞')
                                    ->required()
                                    ->placeholder('+53 5XX-XXXX'),
                                TextInput::make('confirmation_email')
                                    ->label('Correo Electrónico para Confirmaciones')
                                    ->helperText('A este correo se le enviaran las confirmaciones de los pagos realizados en CUP.')
                                    ->prefix('📧')
                                    ->required()
                                    ->placeholder('ejemplo@dominio.com'),
                                TextInput::make('currency_convertion_rate')
                                    ->label('Tasa de Conversión USD a CUP')
                                    ->helperText('Tasa de conversión utilizada para convertir los pagos en USD a CUP para su procesamiento.')
                                    ->numeric()
                                    ->prefix('💱')
                                    ->required()
                                    ->placeholder('500.00'),
                            ]),
                        /*Tabs\Tab::make('APIS')
                            ->icon('heroicon-o-key')
                            ->schema([
                                TextInput::make('eltoque_api_token')
                                    ->label('Token de API de ElToque')
                                    ->helperText('Token de API para integración con ElToque.')
                                    ->prefix('🔑')
                                    ->nullable()
                                    ->placeholder('XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX'),
                            ]),*/
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $setting = Setting::firstOrCreate([]);
        $setting->update([
            'credit_card_info' => $data['credit_card_info'],
            'confirmation_phone' => $data['confirmation_phone'],
            'confirmation_email' => $data['confirmation_email'],
            'currency_convertion_rate' => $data['currency_convertion_rate'],
            /*'eltoque_api_token' => $data['eltoque_api_token'],*/
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

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->formatStateUsing(function($state){
                        Carbon::setLocale('es');
                        return Carbon::parse($state)->translatedFormat('j \d\e F \d\e Y H:m');
                    }),
                TextColumn::make('phone')
                    ->label('Teléfono origen')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Monto del pago')
                    ->prefix('CUP ')
                    ->sortable(),
                TextColumn::make('code')
                    ->label('Nro. Transacción')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Estado de confirmación')
                    ->badge()
                    ->formatStateUsing(fn($state) => match($state) {
                        'pending' => 'Pendiente por confirmar',
                        'paid' => 'Transferencia confirmada',
                        default => $state,
                    })
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                    ]),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente por confirmar',
                        'paid' => 'Transferencia confirmada',
                    ]),
            ])
            ->recordActions([
                Action::make('confirm')
                    ->label('Confirmar')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->button()
                    ->requiresConfirmation()
                    ->modalDescription('¿Estás seguro de que deseas confirmar esta transferencia? Esta acción no se puede deshacer. Al confirmar aceptas haber recibido la transferencia y enviar el producto al cliente.')
                    ->modalSubmitActionLabel('Sí, Confirmar')
                    ->modalCancelActionLabel('No, Cancelar')
                    ->visible(fn($record) => $record->status == 'pending')
                    ->action(function($record){
                        try {
                            
                            $user = User::where('email', $record->customer_email)->first();

                            $token = Str::random(50);

                            $user && $user->notify(new FilePaid(route('order.download', [$record->id, 'token' => $token])));
                            if ($record->customer_email && !$user) {
                                $user = User::where('email', 'user@guest.com')->first();
                                MailNotification::route('mail', $record->customer_email)->notify(new FilePaid(route('order.download', [$record->id, 'token' => $token])));
                            }

                            $downloadToken = $user?->downloadToken ?? [];
                            array_push($downloadToken, $token);
                            $user->downloadToken = $downloadToken;
                            $user->save();
                            
                            $record->update([
                                'status' => 'paid',
                                'paid_at' => Carbon::now(),
                            ]);
                            
                            foreach ($record->order_items as $item) {
                                $sale = new Sale([
                                    'file_id' => $item->file->id,
                                    'amount' => $item->file->price,
                                    'status' => 'paid',
                                    'user_amount' => $item->file->price*1.0,
                                    'admin_amount' => $item->file->price*0,
                                ]);
                                $sale->save();
                            }

                            Notification::make()
                                ->title('Pago confirmado')
                                ->success()
                                ->send();
                        } catch (\Throwable $th) {
                            Notification::make()
                                ->title('Error al confirmar el pago')
                                ->body('Error: '.$th->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll(null)
            ->heading('Transferencias');
    }

    protected function getTableQuery()
    {
        $query = Order::query()->where('currency', 'CUP');

        return $query;
    }
}
