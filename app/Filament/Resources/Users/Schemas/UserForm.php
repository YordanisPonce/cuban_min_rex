<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
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
                Select::make('role')
                    ->label('Rol')
                    ->required()
                    ->options([
                        'admin' => 'Administrador',
                        'user' => 'Usuario',
                        'worker' => 'Trabajador',
                    ])
                    ->default('user'),
            ]);
    }
}
