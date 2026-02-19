<?php

namespace App\Filament\Widgets;

use App\Enums\SectionEnum;
use App\Models\Order;
use App\Models\Sale;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DevRadioStatsWidget extends BaseWidget
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
        $query = Order::query()->where('status','paid')
            ->where('currency', 'USD')
            ->whereHas('order_items', function($q){
                $q->whereHas('file', function($qe){
                    $qe->whereJsonContains('sections', SectionEnum::CUBANDJS->value)->orWhereJsonContains('sections', SectionEnum::CUBANDJS_LIVE_SESSIONS->value);
                });
            });

        $queryCUP = Order::query()->where('status','paid')
            ->where('currency', 'CUP')
            ->whereHas('order_items', function($q){
                $q->whereHas('file', function($qe){
                    $qe->whereJsonContains('sections', SectionEnum::CUBANDJS->value)->orWhereJsonContains('sections', SectionEnum::CUBANDJS_LIVE_SESSIONS->value);
                });
            });

        if ($this->month) {
            $query->whereMonth('created_at', $this->month);
            $queryCUP->whereMonth('created_at', $this->month);
        }

        if ($this->year) {
            $query->whereYear('created_at', $this->year);
            $queryCUP->whereYear('created_at', $this->year);
        }

        $totalSales = $query->count();

        $totalEarning = $query->sum('amount');

        $comision = $totalEarning * 0.1;

        $totalSalesCUP = $queryCUP->count();

        $totalEarningCUP = $queryCUP->sum('amount');

        $comisionCUP = $totalEarningCUP * 0.1;

        return [
            Stat::make('Total de ventas (USD)', $totalSales)
                ->description('Total de archivos vendidos')
                ->descriptionIcon('heroicon-o-document-text', IconPosition::Before)
                ->color('info'),

            Stat::make('Total generado por ventas (USD)', '$ '.$totalEarning)
                ->description('Total de dinero por ventas')
                ->descriptionIcon('heroicon-o-banknotes', IconPosition::Before)
                ->color('primary'),

            Stat::make('Comisión por ventas (USD)', '$ '.$comision)
                ->description('10% de las ventas')
                ->descriptionIcon('heroicon-o-currency-dollar', IconPosition::Before)
                ->color('success'),

            Stat::make('Total de ventas (CUP)', $totalSalesCUP)
                ->description('Total de archivos vendidos')
                ->descriptionIcon('heroicon-o-document-text', IconPosition::Before)
                ->color('info'),

            Stat::make('Total generado por ventas (CUP)', '$ '.$totalEarningCUP)
                ->description('Total de dinero por ventas')
                ->descriptionIcon('heroicon-o-banknotes', IconPosition::Before)
                ->color('primary'),

            Stat::make('Comisión por ventas (CUP)', '$ '.$comisionCUP)
                ->description('10% de las ventas')
                ->descriptionIcon('heroicon-o-currency-dollar', IconPosition::Before)
                ->color('success'),
        ];
    }

    protected function getColumns(): int|array|null
    {
        return 3;
    }
}
