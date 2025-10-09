<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at')
                    ->label('Verificación del Correo'),
                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->required(),
                TextInput::make('role')
                    ->label('Rol')
                    ->required()
                    ->default('user'),
                TextInput::make('stripe_id'),
                TextInput::make('pm_type'),
                TextInput::make('pm_last_four'),
                DateTimePicker::make('trial_ends_at'),
            ]);
    }
}
