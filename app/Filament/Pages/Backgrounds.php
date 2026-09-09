<?php

namespace App\Filament\Pages;

use App\Models\Background;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Pages\Page;
use BackedEnum;
use UnitEnum;

class Backgrounds extends Page
{
    protected string $view = 'filament.pages.backgrounds';

    protected static string|UnitEnum|null $navigationGroup = 'Configuraciones';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Photo;

    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Fondos de Página';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('new')
                ->label('Subir Fondo')
                ->icon(Heroicon::ArrowUpCircle)
                ->modalHeading('Subir Fondo')
                ->modalIcon(Heroicon::ArrowUpCircle)
                ->color('primary')
                ->schema([
                    FileUpload::make('path')
                        ->disk('s3')
                        ->directory('images/backgrounds')
                        ->label('Fondo')
                        ->image()
                        ->required()
                ])
                ->action(function(array $data){
                    try {
                        $background = Background::create($data);

                        Notification::make()
                            ->body('Fondo Subido Correctamente')
                            ->success()
                            ->persistent()
                            ->send();
                    } catch (\Throwable $th) {
                        Notification::make()
                            ->title('Error al Subir Fondo')
                            ->body($th->getMessage())
                            ->error()
                            ->persistent()
                            ->send();
                    }
                }),
        ];
    }

    protected function backgrounds(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Background::orderBy('active', 'desc')->paginate(8);
    }

    public function deleteBackground($id){
        $background = Background::find($id);

        if($background) {
            $background->delete();
            Notification::make()
                ->body('Fondo Eliminado Correctamente')
                ->success()
                ->persistent()
                ->send();
        } else {
            Notification::make()
                ->body('No se encontro el Fondo')
                ->danger()
                ->persistent()
                ->send();
        }
    }

    public function toggleBackground($id){
        $banner = Background::find($id);

        if($banner) {
            $active = !$banner->active;
            $banner->update([
                'active' => $active
            ]);
        }
    }
}
