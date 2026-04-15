<?php

namespace App\Providers\Filament;

use App\Filament\Pages\DevDashboard;
use App\Http\Middleware\CheckUserAccess;
use App\Http\Middleware\DevMiddleware;
use App\Livewire\DownloadsChart;
use App\Livewire\FileDownloadWidget;
use App\Livewire\SubscriptionChart;
use App\Livewire\NewSubscriptionChart;
use App\Livewire\SaleChart;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->brandLogo(new HtmlString('<div style="display: flex; align-items: center; gap: 10px"><img style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; flex-shrink: 0;" src="'.config('app.logo').'" alt="'.config('app.name').'"> '. config('app.name') .'</div>'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                FileDownloadWidget::class,
                SubscriptionChart::class,
                NewSubscriptionChart::class,
                DownloadsChart::class,
                SaleChart::class
            ])
            ->plugins([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                CheckUserAccess::class,
                DevMiddleware::class
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->globalSearch(false)
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                'Cotización',
                'Archivos',
                'Usuarios',
                'Gestión',
                'Configuraciones'
            ]);
        ;
    }
}
