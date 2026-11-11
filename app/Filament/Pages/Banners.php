<?php

namespace App\Filament\Pages;

use App\Models\Banner;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use UnitEnum;

class Banners extends Page
{
    protected string $view = 'filament.pages.banners';

    protected static string|UnitEnum|null $navigationGroup = 'Configuraciones';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Photo;

    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Banners Promocionales';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('new')
                ->label('Subir Banner')
                ->icon(Heroicon::ArrowUpCircle)
                ->modalHeading('Subir Banner')
                ->modalIcon(Heroicon::ArrowUpCircle)
                ->color('primary')
                ->schema([
                    FileUpload::make('path')
                        ->disk('s3')
                        ->directory('images/banners')
                        ->label('Banner')
                        ->acceptedFileTypes(["video/*", "image/*"])
                        ->required()
                        ->helperText('Tener en cuenta que los videos sufrirán los mismos efectos que las imagenes, puede que se corte o no se muestre totalmente. Un banner video puede provocar lentitud a la hora de cargar la web.'),
                    Toggle::make('isVideo')
                        ->onIcon('heroicon-s-check-circle')
                        ->offIcon('heroicon-s-x-circle')
                        ->label('Banner animado')
                        ->helperText('Habilitar o deshabilitar para videos, de lo contrario se mostrara como imagen fija.'),
                ])
                ->action(function(array $data){
                    try {
                        $banner = new Banner();
                        $banner->path = $data['path'];
                        $banner->isVideo = $data['isVideo'];
                        $banner->save();

                        Notification::make()
                            ->body('Banner Subido Correctamente')
                            ->success()
                            ->persistent()
                            ->send();
                    } catch (\Throwable $th) {
                        Notification::make()
                            ->title('Error al Subir Banner')
                            ->body($th->getMessage())
                            ->error()
                            ->persistent()
                            ->send();
                    }
                }),
        ];
    }

    protected function banners(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Banner::orderBy('active', 'desc')->paginate(8);
    }

    public function deleteBanner($id){
        $banner = Banner::find($id);

        if($banner) {
            try {
                $imagePath = Storage::disk('s3')->get($banner->path);
                Storage::disk('s3')->delete($imagePath);
            } catch (\Exception $e) {
                Notification::make()->body('No se pudo eliminar la imagen anterior de AWS S3: ' . $e->getMessage())->warning()->send();
            }
            $banner->delete();
            Notification::make()
                ->body('Banner Eliminado Correctamente')
                ->success()
                ->persistent()
                ->send();
        } else {
            Notification::make()
                ->body('No se encontro el Banner')
                ->danger()
                ->persistent()
                ->send();
        }
    }

    public function toggleBanner($id){
        $banner = Banner::find($id);

        if($banner) {
            $active = !$banner->active;
            $banner->update([
                'active' => $active
            ]);
        }
    }
}
