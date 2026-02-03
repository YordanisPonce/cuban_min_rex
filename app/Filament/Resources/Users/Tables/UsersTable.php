<?php

namespace App\Filament\Resources\Users\Tables;

// use Filament\Actions\BulkActionGroup;
// use Filament\Actions\DeleteBulkAction;

use App\Mail\AccountDeletedMail;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Stripe\StripeClient;
use Throwable;

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
                Filter::make('current_plan_id')
                    ->label('Subscripción activa')
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->whereNot('current_plan_id', null)),
                SelectFilter::make('role')
                    ->label('Rol')
                    ->options([
                        'user' => 'Usuario',
                        'worker' => 'Trabajador',
                        'admin' => 'Administrador',
                    ]),
            ])->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->label('Filtros'),
            )
            ->recordActions([
                EditAction::make()->label('Editar')->visible(fn() => auth()->user()?->role === 'admin'),
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
                /*                 DeleteAction::make()->label('Eliminar')->visible(fn() => auth()->user()?->role === 'admin'), */
                // ✅ DELETE PERSONALIZADO
                Action::make('deleteWithEmail')
                    ->label('Eliminar')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(fn() => auth()->user()?->role === 'admin')
                    ->requiresConfirmation()
                    ->modalHeading('Eliminar cuenta')
                    ->modalDescription('Antes de eliminar, escribe el mensaje que se enviará al usuario por correo.')
                    ->modalSubmitActionLabel('Eliminar y enviar correo')
                    ->modalCancelActionLabel('Cancelar')
                    ->modalWidth('xl')
                    ->schema([
                        TextInput::make('subject')
                            ->label('Asunto del correo')
                            ->required()
                            ->maxLength(120)
                            ->default('Tu cuenta ha sido eliminada'),

                        RichEditor::make('message')
                            ->label('Mensaje para el usuario')
                            ->required()
                            ->helperText('Este texto se enviará al correo del usuario.'),
                    ])
                    ->action(function (User $record, array $data) {
                        // Evita auto-eliminarse (opcional)
                        if (auth()->id() === $record->id) {
                            Notification::make()
                                ->title('Acción no permitida')
                                ->body('No puedes eliminar tu propia cuenta.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $email = $record->email;
                        $name = $record->name;

                        // Sanitiza el HTML del RichEditor
                        $safeHtml = self::sanitizeRichText($data['message']);

                        try {
                            // ✅ 1) Cancelar suscripciones activas/trialing en Stripe (AHÍ MISMO)
                            $cancelledIds = self::cancelStripeSubscriptionsNow($record);

                            // ✅ 2) Enviar correo
                            Mail::to($email)->send(
                                new \App\Mail\AccountDeletedMail(
                                    userName: $name,
                                    title: $data['subject'],
                                    msg: $safeHtml,
                                )
                            );

                            // ✅ 3) Borrar usuario
                            $record->delete();

                            $extra = $cancelledIds
                                ? ' Subs canceladas: ' . implode(', ', $cancelledIds)
                                : ' No había suscripciones activas.';

                            Notification::make()
                                ->title('Cuenta eliminada')
                                ->body('El usuario fue eliminado, se envió el correo y se cancelaron las suscripciones.' . $extra)
                                ->success()
                                ->send();

                        } catch (Throwable $e) {
                            // Si Stripe falla, no borramos al usuario
                            report($e);

                            Notification::make()
                                ->title('No se pudo eliminar')
                                ->body('Ocurrió un error cancelando suscripciones en Stripe o enviando el correo. No se eliminó la cuenta. Detalle: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->closeModalByClickingAway(false),
            ])
            ->toolbarActions([
            ])
            ->modifyQueryUsing(
                fn($record): Builder => auth()->user()?->role === 'admin' ? User::query()->whereNot('role', 'developer')->orderBy('id', 'desc') : User::query()->whereNot('current_plan_id')->orderBy('id', 'desc')
            );
    }



    private static function sanitizeRichText(string $html): string
    {
        // Permite tags típicos del editor, bloquea scripts/eventos.
        $allowed = '<p><br><b><strong><i><em><u><s><blockquote><ul><ol><li><a><h1><h2><h3><h4><h5><h6><span>';

        $html = strip_tags($html, $allowed);

        // Bloquea javascript: en links (básico)
        $html = preg_replace('/href\s*=\s*"javascript:[^"]*"/i', 'href="#"', $html);

        // Quita handlers onClick, onLoad, etc. (básico)
        $html = preg_replace('/\son\w+\s*=\s*"[^"]*"/i', '', $html);

        return $html ?: '<p></p>';
    }

    private static function cancelStripeSubscriptionsNow(User $user): array
    {
        // ✅ Si usas Laravel Cashier, normalmente es $user->stripe_id
        $customerId = $user->stripe_id ?? $user->stripe_customer_id ?? null; // ajusta si tu campo es otro

        if (!$customerId) {
            return [];
        }

        $stripe = new StripeClient(config('services.stripe.secret_key'));

        // Trae subs activas/trialing del customer
        $subs = $stripe->subscriptions->all([
            'customer' => $customerId,
            'status' => 'all',
            'limit' => 100,
        ]);

        $cancelled = [];

        foreach ($subs->data as $sub) {
            // Solo cancelamos las que realmente importan “activas”
            if (!in_array($sub->status, ['active', 'trialing', 'past_due', 'unpaid'], true)) {
                continue;
            }

            // Cancelación inmediata (termina ya)
            $stripe->subscriptions->cancel($sub->id, []);

            $cancelled[] = $sub->id;
        }

        return $cancelled;
    }

}
