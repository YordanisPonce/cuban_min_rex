<?php

namespace App\Livewire;

use App\Models\Download;
use App\Models\File;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class DownloadsChart extends ChartWidget
{
    protected ?string $heading = 'Porciento de Descargas';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        
        $userId = Auth::user()->id;

        $userDownloadsCount = File::where('user_id', $userId)->sum('download_count') ?? 0;

        $totalDownloadsCount = Download::count() > 0 ? Download::count() : 100;

        return [
            'labels' => ['Mis descargas', 'Resto'],
            'datasets' => [
                [
                    'label' => 'Porciento',
                    'data' => [$userDownloadsCount/$totalDownloadsCount * 100, ($totalDownloadsCount-$userDownloadsCount)/$totalDownloadsCount*100],
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
