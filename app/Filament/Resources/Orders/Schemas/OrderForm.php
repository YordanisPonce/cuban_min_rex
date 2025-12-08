<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Tables;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información de la orden')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Usuario')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('plan_id')
                            ->label('Plan')
                            ->relationship('plan', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('file_id')
                            ->label('Archivo')
                            ->relationship('file', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('amount')
                            ->label('Importe')
                            ->numeric()
                            ->prefix('€') // o $, según uses
                            ->required(),

                        Forms\Components\TextInput::make('status')
                            ->label('Estado')
                            ->maxLength(50),

                        Forms\Components\TextInput::make('stripe_session_id')
                            ->label('ID de sesión de Stripe')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('stripe_payment_intent')
                            ->label('Payment Intent de Stripe')
                            ->maxLength(255),

                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Fecha de pago'),

                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Fecha de expiración'),
                    ])->columns(2),
            ]);
    }
}
