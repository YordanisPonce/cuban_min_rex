<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\LegalText;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Schemas\Components\Tabs;
use BackedEnum;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class Legal extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentText;

    protected string $view = 'filament.pages.legal';

    protected static ?string $navigationLabel = 'Textos Legales';

    protected static ?string $title = 'Textos Legales';

    protected static ?int $navigationSort = 9999;

    protected static string|UnitEnum|null $navigationGroup = 'Configuraciones';

    public ?array $data = [];

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
        $setting = LegalText::firstOrCreate([]);
        $this->form->fill([
            'legal' => $setting->legal,
            'cookies' => $setting->cookies,
            'privacy' => $setting->privacy,
            'terms' => $setting->terms,
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        Tabs\Tab::make('Legal')
                            ->schema([
                                RichEditor::make('legal')
                                    ->label('Aviso Legal')
                                    ->extraAttributes(['style' => 'min-height: 25em;'])
                                    ->required()
                                    ->columnSpanFull(),
                        ]),
                        Tabs\Tab::make('Cookies')
                            ->schema([
                                RichEditor::make('cookies')
                                    ->label('Política de Cookies')
                                    ->extraAttributes(['style' => 'min-height: 25em;'])
                                    ->required()
                                    ->columnSpanFull(),
                        ]),
                        Tabs\Tab::make('Privacidad')
                            ->schema([
                                RichEditor::make('privacy')
                                    ->label('Política de Privacidad')
                                    ->extraAttributes(['style' => 'min-height: 25em;'])
                                    ->required()
                                    ->columnSpanFull(),
                        ]),
                        Tabs\Tab::make('Terms')
                            ->label('Términos')
                            ->schema([
                                RichEditor::make('terms')
                                    ->label('Términos y Condiciones')
                                    ->extraAttributes(['style' => 'min-height: 25em;'])
                                    ->required()
                                    ->columnSpanFull(),
                        ])
                    ])
                    ->extraAttributes(['style' => 'width: 100%;'])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $setting = LegalText::firstOrCreate([]);
        $setting->update([
            'legal' => $data['legal'],
            'cookies' => $data['cookies'],
            'privacy' => $data['privacy'],
            'terms' => $data['terms'],
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
