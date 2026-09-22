<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Schemas\Components\Tabs;
use BackedEnum;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use UnitEnum;

class AndroidSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-device-phone-mobile';

    protected string $view = 'filament.pages.android-settings';

    protected static ?string $navigationLabel = 'Android APP';

    protected static ?string $title = 'Android APP';

    protected static ?int $navigationSort = 9999;

    protected static string|UnitEnum|null $navigationGroup = 'Configuraciones';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role == 'admin' || auth()->user()?->role == 'developer';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->role == 'admin' || auth()->user()?->role == 'developer';
    }

    public function mount(): void
    {
        $setting = Setting::firstOrCreate([]);
        $this->form->fill([
            'android_app_version' => $setting->android_app_version,
            'android_app_download_path' => $setting->android_app_download_path,
            'android_app_can_be_download' => $setting->android_app_can_be_download
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        Tabs\Tab::make('Android App')
                            ->icon('heroicon-o-device-phone-mobile')
                            ->schema([
                                TextInput::make('android_app_version')
                                    ->label('Version de la APP')
                                    ->prefix('v')
                                    ->nullable()
                                    ->placeholder('1.0.1+'),
                                TextInput::make('android_app_download_path')
                                    ->label('Enlace de descarga de la APP')
                                    ->helperText('Enlace al que se redigirá a los usuarios para descargar la aplicación.')
                                    ->prefix('🌐')
                                    ->nullable()
                                    ->placeholder('https://mega.nz/...'),
                                Toggle::make('android_app_can_be_download')
                                    ->onIcon('heroicon-s-check-circle')
                                    ->offIcon('heroicon-s-x-circle')
                                    ->label('Mostrar a los usuarios')
                                    ->helperText('Habilitar o deshabilitar el banner de promoción de la aplicación en la página.'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $setting = Setting::firstOrCreate([]);
        $setting->update([
            'android_app_version' => $data['android_app_version'],
            'android_app_download_path' => $data['android_app_download_path'],
            'android_app_can_be_download' => $data['android_app_can_be_download'],
        ]);

        Notification::make()
            ->title('Configuración guardada')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Guardar Configuración')
                ->action('save'),
        ];
    }
}
