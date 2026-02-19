<?php

namespace App\Filament\Widgets;

use App\Enums\SectionEnum;
use App\Models\Order;
use App\Models\Sale;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DevStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    public ?string $month = null;
    public ?string $year = null;

    public static function canView(): bool
    {
        return auth()->user()?->role==='developer' ?? false;
    }

    public function mount(?string $month = null, ?string $year = null): void
    {
        $this->month = $month;
        $this->year = $year;
    }

    protected function getStats(): array
    {
        $query = Sale::query()->whereHas('file', function($q){
            $q->whereJsonContains('sections', SectionEnum::MAIN->value);
        });

        $orderQuery = Order::query()->whereHas('plan')->where('status', 'paid');

        if ($this->month) {
            $query->whereMonth('created_at', $this->month);
            $orderQuery->whereMonth('created_at', $this->month);
        }

        if ($this->year) {
            $query->whereYear('created_at', $this->year);
            $orderQuery->whereYear('created_at', $this->year);
        }

        $totalSales = $query->count();

        $totalEarning = $query->sum('amount');

        $comision = $totalEarning * 0.2;

        $plans = $orderQuery->count();

        $plansAmount = $orderQuery->sum('amount');

        $plansComision = $plansAmount * 0.2;

        return [
            Stat::make('Total de ventas', $totalSales)
                ->description('Total de archivos vendidos')
                ->descriptionIcon('heroicon-o-document-text', IconPosition::Before)
                ->color('info'),

            Stat::make('Total generado por ventas', '$ '.$totalEarning)
                ->description('Total de dinero por ventas')
                ->descriptionIcon('heroicon-o-banknotes', IconPosition::Before)
                ->color('primary'),

            Stat::make('Comisión por ventas', '$ '.$comision)
                ->description('20% de las ventas')
                ->descriptionIcon('heroicon-o-currency-dollar', IconPosition::Before)
                ->color('success'),

            Stat::make('Total de suscripciones', $plans)
                ->description('Total de suscripciones vendidas')
                ->descriptionIcon('heroicon-o-user-group', IconPosition::Before)
                ->color('info'),

            Stat::make('Total generado por suscripciones', '$ '.$plansAmount)
                ->description('Total de dinero por suscripciones')
                ->descriptionIcon('heroicon-o-banknotes', IconPosition::Before)
                ->color('primary'),

            Stat::make('Comision por suscripciones', '$ '.$plansComision)
                ->description('20% de las suscripciones')
                ->descriptionIcon('heroicon-o-currency-dollar', IconPosition::Before)
                ->color('success'),

            Stat::make('Comision total', '$ '.$plansComision + $comision)
                ->description('Comision Ventas + Suscripciones')
                ->descriptionIcon('heroicon-o-currency-dollar', IconPosition::Before)
                ->color('success'),
        ];
    }

    protected function getColumns(): int|array|null
    {
        return 3;
    }
}
