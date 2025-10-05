<?php

namespace App\Livewire;

use App\Models\Collection;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $activeSubscriptions = DB::table('subscriptions')
                ->where(function($query) {
                    $query->where('created_at', '<=', Carbon::now());
                })
                ->where(function($query) {
                    $query->whereNull('ends_at')
                          ->orWhere('ends_at', '>', Carbon::now());
                })
                ->where(function($query) {
                    $query->whereNull('canceled_at')
                          ->orWhere('canceled_at', '>', Carbon::now());
                })
                ->count();
        $salesCount = Auth()->user()->sales()->count();

        $stats = [
            Stat::make('Cantidad de Descargas', $downloadCounts),
            Stat::make('Comisión por descargas', '21%'),
            Stat::make('Cantidad de Ventas', $salesCount),
            Stat::make('Archivo más descargado', $fileMoreDownload ? $fileMoreDownload->name : 'Desconocido'),
            Stat::make('Colección más descargada', $collectionMoreDownload ? $collectionMoreDownload->name : 'Desconocido'),
        ];

        if(Auth()->user()->role === 'admin'){
            $stats[] = Stat::make('Subscripciones Activas', $activeSubscriptions);
        }

        return $stats;
    }
}