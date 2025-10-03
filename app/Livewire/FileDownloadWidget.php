<?php

namespace App\Livewire;

use App\Models\Collection;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\File;
use Illuminate\Support\Facades\Auth;

class FileDownloadWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id();
        $downloadCounts = File::where('user_id', $userId)->sum('download_count');
        $fileMoreDownload = File::where('user_id', $userId)
                   ->orderBy('download_count', 'desc')
                   ->first();
        $collectionMoreDownload = Collection::where('user_id', $userId)
                   ->orderBy('download_count', 'desc')
                   ->first();
        return [
            Stat::make('Cantidad de Descargas', $downloadCounts),
            Stat::make('Comisión por descargas', '21%'),
            Stat::make('Archivo más descargado', $fileMoreDownload ? $fileMoreDownload->name : 'Desconocido'),
            Stat::make('Coleción más descargada', $collectionMoreDownload ? $collectionMoreDownload->name : 'Desconocido'),
        ];
    }
}