<?php

namespace App\Livewire;

use App\Models\Collection;
use App\Models\Download;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\File;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Subscription;

class FileDownloadWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id();

        $downloadCounts = Download::whereHas('file', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();

        Stripe::setApiKey(config('services.stripe.secret_key'));
        $activeSubscriptions = Subscription::all(['status' => 'active'])->count();
        $salesCount = Sale::whereHas('file', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();

        $stats = [
            Stat::make('Cantidad de Descargas', $downloadCounts),
            Stat::make('Cantidad de descargas por liquidar', auth()->user()->totalUnliquidatedDownloads()),
            Stat::make('Cantidad de Ventas', $salesCount),
            Stat::make('Pendiente por cobrar', '$ ' . auth()->user()->pendingSubscriptionLiquidation() + auth()->user()->pendingSalesTotal()),
            Stat::make('Ganancia Total', '$ ' . auth()->user()->paidSalesTotal() + auth()->user()->paidSubscriptionLiquidation()),
            Stat::make('Subscripciones Activas', $activeSubscriptions)
        ];

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