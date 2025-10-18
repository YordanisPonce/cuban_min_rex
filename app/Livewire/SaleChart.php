<?php

namespace App\Livewire;

use App\Models\Sale;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class SaleChart extends ChartWidget
{
    protected ?string $heading = 'Relación de ventas';
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Rango: últimos 12 meses incluyendo el actual
        $start = now()->startOfMonth()->subMonths(11);
        $end = now()->endOfMonth();

        // Claves de mes para ordenar y mapear resultados: 'YYYY-MM'
        $monthKeys = collect(range(0, 11))->map(
            fn($i) => $start->copy()->addMonths($i)->format('Y-m')
        );

        // Etiquetas en español, alineadas al rango dinámico
        $monthNamesEs = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $labels = $monthKeys->map(function ($ym) use ($monthNamesEs) {
            [$y, $m] = explode('-', $ym);
            return $monthNamesEs[(int) $m - 1];
        })->all();

        // Consulta: conteo y suma por mes (MySQL)
        $rows = Sale::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as total, COALESCE(SUM(amount),0) as suma")
            ->whereBetween('created_at', [$start, $end])
            ->where(function (Builder $query) {
                auth()->user()->role != 'admin' && $query->whereHas('file', fn(Builder $q) => $q->where('user_id', auth()->id()));
            })
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->keyBy('ym');

        // Mapear a los 12 meses, rellenando faltantes con 0
        $countData = $monthKeys->map(fn($k) => (int) ($rows[$k]->total ?? 0))->all();
        $sumData = $monthKeys->map(fn($k) => (float) ($rows[$k]->suma ?? 0))->all();

        return [
            'labels' => $labels, // p.ej.: ['Nov','Dic','Ene',...]
            'datasets' => [
                [
                    'label' => 'Ventas',
                    'data' => $countData,
                    'backgroundColor' => ['rgba(54, 162, 235, 0.5)'],
                    'borderColor' => ['rgba(54, 162, 235, 1)'],
                    'borderWidth' => 1,
                ],
                // ---- OPCIONAL: muestra el monto total mensual ----
                [
                    'type' => 'line',
                    'label' => 'Monto total (ventas)',
                    'data' => $sumData,
                    'yAxisID' => 'y1',
                    'tension' => 0.3,
                    'borderWidth' => 2,
                ],
            ],
            // Ejes extra para que el monto no "aplane" el conteo
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'scales' => [
                    'y' => ['beginAtZero' => true, 'position' => 'left'],
                    'y1' => [
                        'beginAtZero' => true,
                        'position' => 'right',
                        'grid' => ['drawOnChartArea' => false],
                    ],
                ],
                'plugins' => [
                    'tooltip' => [
                        'mode' => 'index',
                        'intersect' => false,
                    ],
                    'legend' => ['display' => true],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        // Al tener datasets mixtos, Chart.js respeta el 'type' de cada dataset.
        return 'bar';
    }
}
