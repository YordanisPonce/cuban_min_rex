<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NewSubscriptionChart extends ChartWidget
{
    protected ?string $heading = 'Nuevas Subscripciones';

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
        
        $registrosPorMes = array_fill(0, 12, 0);
        $cancelacionesPorMes = array_fill(0, 12, 0);

        $resultados = DB::table('subscriptions')
            ->select(DB::raw('MONTH(created_at) as month, COUNT(*) as total'))
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        foreach ($resultados as $resultado) {
            $registrosPorMes[$resultado->month - 1] = $resultado->total;
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
                    'label' => 'Nuevas subscripciones',
                    'data' => $registrosPorMes,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],[
                    'label' => 'Subscripciones canceladas',
                    'data' =>  $cancelacionesPorMes,
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
