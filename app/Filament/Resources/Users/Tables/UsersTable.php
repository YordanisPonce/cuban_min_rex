<?php

namespace App\Filament\Resources\Users\Tables;

// use Filament\Actions\BulkActionGroup;
// use Filament\Actions\DeleteBulkAction;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Correo Electrónico')
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->label('Confirmación del Correo')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('role')
                    ->label('Rol')
                    ->searchable()
                    ->getStateUsing(fn($record) => $record->role === 'admin' ? 'Administrador' : ($record->role === 'worker' ? 'Trabajador' : 'Usuario')),
                TextColumn::make('currentPlan.name')
                    ->label('Subscripción Activa'),
                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Fecha de Actuaización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()->label('Editar'),
                Action::make('changePassword')->visible(fn() => auth()->user()?->role === 'admin')
                    ->modalSubmitActionLabel('Cambiar')
                    ->modalCancelActionLabel('Cancelar')
                    ->label('Cambiar contraseña')
                    ->icon('heroicon-o-key')
                    ->color('info')
                    ->modalHeading('Asignar nueva contraseña')
                    ->modalWidth('sm')
                    ->schema([
                        TextInput::make('password')
                            ->label('Nueva contraseña')
                            ->password()
                            ->revealable()
                            ->required()
                            // Regla de seguridad de Laravel (ajústala si quieres algo más estricto)
                            ->rule(Password::defaults())
                            ->minLength(8),

                        TextInput::make('password_confirmation')
                            ->label('Confirmar contraseña')
                            ->password()
                            ->revealable()
                            ->required()
                            // Valida que coincida con 'password'
                            ->same('password'),
                    ])
                    ->action(function ($record, array $data) {
                        // $data['password'] ya está validado y confirmado
                        $record->forceFill([
                            'password' => Hash::make($data['password']),
                        ])->save();

                        Notification::make()
                            ->title('Contraseña actualizada')
                            ->body('La contraseña del usuario se cambió correctamente.')
                            ->success()
                            ->send();
                    })
                    // Opcional: evita cerrar el modal si hay error de validación
                    ->closeModalByClickingAway(false),
                DeleteAction::make()->label('Eliminar'),
            ])

            ->toolbarActions([
            ])
            ->modifyQueryUsing(
                fn(): Builder => User::query()->orderBy('id', 'desc')
            );
    }
}
