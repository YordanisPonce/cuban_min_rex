<?php

namespace App\Filament\Resources\BlockUsers\Tables;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class BlockUsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nombre'),
                TextColumn::make('email')->label('Correo'),
                TextColumn::make('block_reason')->label('Razón'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('unblock')
                    ->label('Desbloquear')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function($record) {

                        $email = $record->email;
                        $name = $record->name;

                        try {

                            Mail::to($email)->send(
                                new \App\Mail\AccountDeletedMail(
                                    userName: $name,
                                    title: 'Cuenta Desbloqueada',
                                    msg: 'Tú cuenta ha sido desbloqueada, ya puedes acceder a nuestra plataforma.',
                                )
                            );

                            $record->update([
                                'is_block' => 0,
                                'block_reason' => null,
                            ]);

                            Notification::make()
                                ->title('Cuenta Desbloqueada')
                                ->body('El usuario fue desbloqueado, se envió el correo.')
                                ->success()
                                ->send();

                        } catch (\Throwable $e) {

                            Notification::make()
                                ->title('No se pudo desbloquear')
                                ->body('Ocurrió un error. No se desbloqueo la cuenta. Detalle: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                //
            ]);
    }
}
