<?php

namespace App\Livewire;

use App\Models\Collection;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\File;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Subscription;

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
        Stripe::setApiKey(config('services.stripe.secret_key'));
        $activeSubscriptions = Subscription::all(['status' => 'active']);
        $activeSubscriptions = User::get()->filter(fn($item) => $item->hasActivePlan())->count();
        $salesCount = Auth()->user()->sales()->count();
        $totalEarningsAtSubscription = (float) Auth()->user()->paidSubscriptionLiquidation();
        $totalEarningsAtSales = (float) Auth()->user()->paidSaleLiquidation();
        $totalEarning = (float) ($totalEarningsAtSubscription + $totalEarningsAtSales);

        $fileMoreDownload = $fileMoreDownload ? $fileMoreDownload->name : 'Desconocido';
        $stats = [
            Stat::make('Cantidad de Descargas', $downloadCounts),
            Stat::make('Ganancia este Mes', '$ ' . $totalEarning),
            Stat::make('Ganancia Total', '$ ' . auth()->user()->totalEarning()),
            Stat::make('Cantidad de Ventas', $salesCount),
            Stat::make('Colección más descargada', $collectionMoreDownload ? $collectionMoreDownload->name : 'Desconocido'),
        ];

        if (Auth()->user()->role === 'admin') {
            $stats[] = Stat::make('Subscripciones Activas', $activeSubscriptions);
        }

        return $stats;
    }
    public function ellipsis(string $text, int $limit = 40): string
    {
        $text = trim($text);
        if (mb_strlen($text, 'UTF-8') <= $limit) {
            return $text;
        }
        return rtrim(mb_substr($text, 0, $limit - 3, 'UTF-8')) . '...';
    }
}