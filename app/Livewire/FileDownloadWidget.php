<?php

namespace App\Livewire;

use App\Filament\Pages\SaleSumary;
use App\Filament\Pages\SuscriptionComisionDetails;
use App\Models\Download;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Sale;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Enums\IconPosition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Stripe\Stripe;
use Stripe\Subscription;

class FileDownloadWidget extends BaseWidget
{
    //protected string $view = 'filament.widgets.file-download-widget';

    protected function getStats(): array
    {
        $userId = Auth::id();

        $downloadCounts = /*Download::whereHas('file', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count()*/ User::find($userId)->getDistinctDownloadsRecived();

        Stripe::setApiKey(config('services.stripe.secret_key'));
        $activeSubscriptions = Subscription::all(['status' => 'active'])->count();
        $salesCount = Sale::whereHas('file', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();

        $stats = [
            Stat::make('Cantidad de Descargas', $downloadCounts)
                ->description('Descargas únicas, sin duplicados.')
                ->descriptionColor('info')
                ->descriptionIcon('heroicon-o-information-circle', IconPosition::Before)->url(SaleSumary::getUrl()),
            Stat::make('Cantidad de descargas por liquidar', auth()->user()->totalUnliquidatedDownloads())
                ->description('Descargas pendientes por cobrar')
                ->descriptionColor('info')
                ->descriptionIcon('heroicon-o-information-circle', IconPosition::Before),
            Stat::make('Cantidad de Ventas', $salesCount)->url(SaleSumary::getUrl())
                ->description('Archivos vendidos')
                ->descriptionColor('success')
                ->descriptionIcon('heroicon-o-musical-note', IconPosition::Before),
            Stat::make('Pendiente por cobrar (Ventas)', '$ ' . auth()->user()->pendingSalesTotal())->description('Cobros por ventas')->descriptionIcon('heroicon-o-currency-dollar', IconPosition::Before)->descriptionColor('success'),
            Stat::make('Pendiente por cobrar (Suscripción)', '$ ' . auth()->user()->pendingSubscriptionLiquidation())
                ->description('Depende de las descargas (ver detalles)')
                ->descriptionColor('info')
                ->descriptionIcon('heroicon-o-information-circle', IconPosition::Before)
                ->url(SuscriptionComisionDetails::getUrl()),
            Stat::make('Ganancia Total', '$ ' . auth()->user()->paidSalesTotal() + auth()->user()->paidSubscriptionLiquidation())->description('Ventas + Suscripciones')->descriptionIcon('heroicon-o-banknotes', IconPosition::Before)->descriptionColor('success'),
            Stat::make('Subscripciones Activas', $activeSubscriptions)
        ];

        return $stats;
    }
    
    protected function getColumns(): int
    {
        return 3;
    }
    
    public function ellipsis(string $text, int $limit = 40): string
    {
        $text = trim($text);
        if (mb_strlen($text, 'UTF-8') <= $limit) {
            return $text;
        }
        return rtrim(mb_substr($text, 0, $limit - 3, 'UTF-8')) . '...';
    }

    public function showDetails(){
        Notification::make()
            ->title("Comisión por descargas")
            ->body(new HtmlString("<div style='text-align: justify; display:flex; flex-direction: column; gap:10px;'>
                <p>La comisión por descargas de una suscripción no es un valor fijo, este se ve afectado por la cantidad de descargas que el usuario realizó sobre tus archivos con respecto a las descargas totales realizadas por dicho usuario durante el período de suscripción. </p>
                <p>Si un usuario descarga 10 canciones, y las 10 son tuyas. Tu comisión sería del 100% a repartir.  </p>
                <p>Pero si el usuario realizó 100 descargas y solo 10 son tuyas, tu comisión será del 10%.  </p>
                <p>Por eso este valor nunca es estático y siempre está en constante variación.</p>
                <p>Descargas repetidas de un mismo usuario sobre un mismo archivo no se tendrán en cuenta.</p>
            </div>"))
            ->info()
            ->persistent()
            ->send();
    }
}