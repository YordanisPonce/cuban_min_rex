<?php

namespace App\Filament\Pages;

use App\Models\SeoText as model;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class SeoText extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.seo-text';

    protected static ?string $navigationLabel = 'SEO';

    protected static ?string $title = 'SEO';

    protected static ?int $navigationSort = 9999;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Configuraciones';

    public ?array $data = [];

    public ?model $setting;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role == 'admin';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->role == 'admin' ?? false;
    }

    public function mount(): void
    {
        $this->setting = model::firstOrCreate([]);
        $this->form->fill([
            'app_name' => $this->setting->app_name,
            'app_keywords' => $this->setting->app_keywords,
            'app_description' => $this->setting->app_description,
            'app_logo' => $this->setting->app_logo,
            'contact_email' => $this->setting->contact_email,
            'contact_phone' => $this->setting->contact_phone,
            'contact_instagram' => $this->setting->contact_instagram,
            'contact_youtube' => $this->setting->contact_youtube,
            'contact_facebook' => $this->setting->contact_facebook,
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        Tabs\Tab::make('Información del Sitio')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                TextInput::make('app_name')
                                    ->label('Nombre sel Sitio'),
                                Textarea::make('app_keywords')
                                    ->label('Palabras Claves'),
                                Textarea::make('app_description')
                                    ->label('Descripción sel Sitio'),
                                FileUpload::make('app_logo')
                                    ->label('Logo del Sitio')
                                    ->image()
                                    ->disk('s3')
                                    ->directory('images/logo')
                                    ->previewable()
                                    ->helperText('Debe ser de 30x30px y de fondo transparente. Subir una foto sin estas especificaciones afectará la estética del sitio.'),
                            ]),
                        Tabs\Tab::make('Información de Contacto')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                TextInput::make('contact_email')
                                    ->label('Email de Contacto'),
                                TextInput::make('contact_phone')
                                    ->label('Teléfono de Contacto'),
                                TextInput::make('contact_instagram')
                                    ->label('Instagram')
                                    ->prefix('https://www.instagram.com/'),
                                TextInput::make('contact_youtube')
                                    ->label('YouTube')
                                    ->prefix('https://www.youtube.com/@'),
                                TextInput::make('contact_facebook')
                                    ->label('FaceBook')
                                    ->prefix('https://www.facebook.com/@'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $setting = model::firstOrCreate([]);
        $setting->update([
            'app_name' => $data['app_name'],
            'app_keywords' => $data['app_keywords'],
            'app_description' => $data['app_description'],
            'app_logo' => $data['app_logo'],
            'contact_email' => $data['contact_email'],
            'contact_phone' => $data['contact_phone'],
            'contact_instagram' => $data['contact_instagram'],
            'contact_youtube' => $data['contact_youtube'],
            'contact_facebook' => $data['contact_facebook'],
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

    protected function logoUrl(){
        $setting = model::firstOrCreate([]);
        return $setting->logoUrl();
    }
}
