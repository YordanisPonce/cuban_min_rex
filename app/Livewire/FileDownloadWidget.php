<?php

namespace App\Livewire;

use App\Models\Collection;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\File;
use App\Models\User;
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
        $activeSubscriptions = User::get()->filter(fn($item) => $item->hasActivePlan())->count();
        $salesCount = Auth()->user()->sales()->count();
        $totalEarningsAtSubscription = (float) Auth()->user()->paidSubscriptionLiquidation();
        $totalEarningsAtSales = (float) Auth()->user()->paidSaleLiquidation();
        $totalEarning = (float) ($totalEarningsAtSubscription + $totalEarningsAtSales);

        $stats = [
            Stat::make('Cantidad de Descargas', $downloadCounts),
            Stat::make('Ganancia Total', '$ '.$totalEarning),
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