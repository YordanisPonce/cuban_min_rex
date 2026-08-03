<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Http\Controllers\NotificationController;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('asign_plan')->label('Asignar Plan')
            ->requiresConfirmation()
            ->modalHeading('¿Quieres asignar un plan a un usuario?')
            ->modalDescription('Asignar un plan a un usuario por esta vía le otorgará los beneficion de la suscripción sin necesidad de pasar por la pasarela de pago. Si recibió el pago por vía de terceros, asegurece primero de recibir el pago antes de otorgar dichos beneficios.')
            ->schema([
                Select::make('user_id')->label('Usuario')->options(function(){
                    $options = [];

                    foreach (User::where('current_plan_id', null)->get() as $user) {
                        $options[$user->id] = $user->name;
                    }

                    return $options;
                })
                ->searchable()
                ->required(),
                Select::make('plan_id')->label('Plan')->options(function(){
                    $options = [];

                    foreach (Plan::all() as $plan) {
                        $options[$plan->id] = $plan->name;
                    }

                    return $options;
                })
                ->searchable()
                ->helperText('Los beneficios serán vigentes desde la fecha actual, y vencerán pasados los meses establecidos por el plan.')
                ->required(),
            ])
            ->action(function(array $data){
                $user = User::find($data['user_id']);
                $plan = Plan::find($data['plan_id']);
                if ($plan && $user) {
                    try {
                        $user->update([
                            'current_plan_id' => $plan->id,
                            'plan_start_at' => now(),
                            'plan_expires_at' => Carbon::now()->addMonths($plan->duration_months)
                        ]);
                        Notification::make()
                        ->success()
                        ->body("Plan {$plan->name} asignado a {$user->name}")
                        ->persistent()
                        ->send();
                    } catch (\Throwable $th) {
                        Notification::make()
                        ->danger()
                        ->title('Error al asignar el plan al usuario')
                        ->body('Error: '. $th->getMessage())
                        ->persistent()
                        ->send();
                    }
                } else {
                    Notification::make()
                    ->danger()
                    ->body('No se han encontrado el usuario y/o plan seleccionado')
                    ->persistent()
                    ->send();
                }
            })
            ->visible(auth()->user()->role==='admin'),
            Action::make('send_ntf')->label('')
            ->icon('heroicon-s-envelope')
            ->requiresConfirmation()
            ->modalHeading('¿Enviar Notificación?')
            ->modalDescription('Esta acción enviará una notificación al usuario seleccionado. Estas notificaciones serán de tipo informativo (Sistema).')
            ->schema([
                Select::make('user_id')->label('Usuario')->options(function(){
                    $options = [];

                    foreach (User::orderBy('name', 'asc')->get() as $user) {
                        $options[$user->id] = $user->name . ' (' . $user->email . ')';
                    }

                    return $options;
                })
                ->searchable()
                ->helperText('Dejar vacío para enviar a todos los usuarios'),
                TextInput::make('title')->label('Título')->required(),
                Textarea::make('message')->label('Mensaje')->required(),
            ])
            ->action(function(array $data){
                if (!$data['user_id']) {
                    try {

                        foreach (User::whereNotNull('email_verified_at')->get() as $user) {
                            NotificationController::sendSistemNtf(
                                $user->id,
                                $data['title'],
                                $data['message'],
                            );
                        }

                        Notification::make()
                        ->success()
                        ->body("Notificación enviada a todos los usuarios.")
                        ->persistent()
                        ->send();
                    } catch (\Throwable $th) {
                        Notification::make()
                        ->danger()
                        ->body('No se ha podido enviar la notificación a todos los usuarios. Error: '. $th->getMessage())
                        ->persistent()
                        ->send();
                    }
                } else {
                    $user = User::find($data['user_id']);
                    if ($user) {
                        try {
                            NotificationController::sendSistemNtf(
                                $user->id,
                                $data['title'],
                                $data['message'],
                            );
                            Notification::make()
                            ->success()
                            ->body("Notificación enviada a {$user->name}")
                            ->persistent()
                            ->send();
                        } catch (\Throwable $th) {
                            Notification::make()
                            ->danger()
                            ->title('Error al enviar la notificación al usuario')
                            ->body('Error: '. $th->getMessage())
                            ->persistent()
                            ->send();
                        }
                    } else {
                        Notification::make()
                        ->danger()
                        ->body('No se ha encontrado el usuario')
                        ->persistent()
                        ->send();
                    }
                }
            })
            ->visible(auth()->user()->role==='admin'),
            CreateAction::make()->label('')
            ->icon('heroicon-o-user-plus'),
        ];
    }
}
