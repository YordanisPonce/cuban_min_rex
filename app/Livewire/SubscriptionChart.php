<?php

namespace App\Livewire;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SubscriptionChart extends ChartWidget
{
    protected ?string $heading = 'Evolución de las Subscripciones';

    protected string $color = 'info';

    public ?string $filter = 'year';

    public static function canView(): bool
    {
        return auth()->user()->role === 'admin';
    }

    protected function getData(): array
    { 

        $activeFilter = $this->filter;
        $year = Carbon::now()->year;

        if($activeFilter === 'last'){
            $year = Carbon::now()->year -1;
        }

        $activosPorMes = array_fill(0, 12, 0);
        $cancelacionesPorMes = array_fill(0, 12, 0);

        for ($month = 1; $month <= 12; $month++) {

            $firstDay = Carbon::createFromDate($year, $month, 1);
            $lastDay = $firstDay->endOfMonth();

            $contador = DB::table('subscriptions')
                ->where(function($query) use ($firstDay) {
                    $query->where('created_at', '<=', $firstDay);
                })
                ->where(function($query) use ($lastDay) {
                    $query->whereNull('ends_at')
                          ->orWhere('ends_at', '>', $lastDay);
                })
                ->where(function($query) use ($lastDay) {
                    $query->whereNull('canceled_at')
                          ->orWhere('canceled_at', '>', $lastDay);
                })
                ->count();

            $activosPorMes[$month - 1] = $contador;
        }

        $resultados = DB::table('subscriptions')
            ->select(DB::raw('MONTH(canceled_at) as month, COUNT(*) as total'))
            ->whereYear('canceled_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        foreach ($resultados as $resultado) {
            $cancelacionesPorMes[$resultado->month - 1] = $resultado->total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Subscripciones Activas',
                    'data' => $activosPorMes,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],[
                    'label' => 'Subscripciones canceladas',
                    'data' => $cancelacionesPorMes,
                    'backgroundColor' => '#EB2222',
                    'borderColor' => '#F52222',
                ],
            ],
            'labels' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'last' => 'Año pasado',
            'year' => 'Este año',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
