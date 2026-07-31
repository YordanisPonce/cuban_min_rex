<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
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
            CreateAction::make()->label('Nuevo Usuario'),
        ];
    }
}
