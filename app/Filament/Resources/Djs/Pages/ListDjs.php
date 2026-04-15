<?php

namespace App\Filament\Resources\Djs\Pages;

use App\Filament\Resources\Djs\DjResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListDjs extends ListRecords
{
    protected static string $resource = DjResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('dispach')
                ->label('Dar de Baja')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->visible(auth()->user()->role === 'admin')
                ->requiresConfirmation()
                ->modalHeading('Dar de Baja a DJ')
                ->modalDescription('¿Seguro de hacer esto? No se podrá revertir.')
                ->modalSubmitActionLabel('Dar de Baja')
                ->form([
                    Select::make('dj_id')
                        ->label('DJ a dar de Baja')
                        ->options(function(){
                            $options = [];

                            $djs = User::where('role', 'worker')->get();
                            foreach ($djs as $d) {
                                $options[$d->id] = $d->name;
                            }

                            return $options;
                        })
                        ->searchable()
                        ->preload()
                        ->required()
                        ->helperText('Una vez dado de baja, todo el contenido subido por este dj será eliminado. Esto incluye remixes, packs, videos, playlist, generos y demás contenido.'),
                    Checkbox::make('only_content')
                        ->label('Solo eliminar el contenido')
                        ->default(false),
                ])
                ->action(function(array $data){
                    $dj = User::find($data['dj_id']);

                    if($dj){

                        $fc = 0;
                        $pc = 0;
                        $n = $dj->name;

                        foreach ($dj->files as $f) {
                            $f->delete();
                            $fc++;
                        }

                        foreach ($dj->playlists as $f) {
                            $f->delete();
                            $pc++;
                        }

                        if(!$data['only_content']){
                            $dj->delete();
                        }

                        Notification::make()
                            ->success()
                            ->title('DJ dado de Baja')
                            ->body($n.' dado de baja, fueron eliminados '.$fc.' remixes y '.$pc.' playlist.')
                            ->persistent()
                            ->send();
                    } else {
                        Notification::make()
                            ->danger()
                            ->body('DJ no encontrado')
                            ->persistent()
                            ->send();
                    }
                }),
        ];
    }
}
