<?php

namespace App\Filament\Widgets;

use App\Enums\SectionEnum;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RadioSalesByMonthChart extends ChartWidget
{
    protected ?string $heading = 'Comision por ventas al mes (USD)';

    protected ?string $description = 'Información sensible al filtro de año.';

    protected static ?int $sort = 3;

    protected ?string $maxHeight = '300px';

    protected static bool $isLazy = false;

    public ?string $year = null;

    public static function canView(): bool
    {
        return auth()->user()?->role === 'developer';
    }

    public function mount(?string $year = null): void
    {
        $this->year = $year ?? Carbon::now()->year;
    }

    protected function getData(): array
    {
        $year = $this->year;

        $query = Order::query()->where('status','paid')
            ->where('currency', 'USD')
            ->whereHas('order_items', function($q){
                $q->whereHas('file', function($q){
                    $q->whereJsonContains('sections', SectionEnum::CUBANDJS->value)->orWhereJsonContains('sections', SectionEnum::CUBANDJS_LIVE_SESSIONS->value);
                });
            });;

        if ($year){
            $query->whereYear('created_at', $year);
        }

        $data = $query->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(amount) * 0.1 as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $months = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        $chartData = [];
        foreach ($months as $monthNum => $monthName) {
            $chartData[] = $data[$monthNum] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Comisión por Ventas',
                    'data' => $chartData,
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#059669',
                ],
            ],
            'labels' => array_values($months),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}