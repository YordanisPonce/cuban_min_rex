<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
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
        $query = Sale::query();

        if ($this->month) {
            $query->whereMonth('created_at', $this->month);
        }

        if ($this->year) {
            $query->whereYear('created_at', $this->year);
        }

        $totalSales = $query->count();

        $totalEarning = $query->sum('amount');

        $comision = $totalEarning * 0.2;

        return [
            Stat::make('Total de Ventas', $totalSales)
                ->description('Total de archivos vendidos')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('info'),

            Stat::make('Total Generado', '$ '.$totalEarning)
                ->description('Total de dinero por ventas')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('primary'),

            Stat::make('Comisión Total', '$ '.$comision)
                ->description('20% de las ventas')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success'),
        ];
    }
}
