<?php

namespace App\Filament\Pages;

use App\Livewire\LiquidationsTableWidget;
use App\Livewire\TabsWidget;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Liquidations extends Page
{
    protected string $view = 'filament.pages.liquidations';

    protected static ?string $title = 'Liquidaciones';
    
    protected static BackedEnum|string|null $navigationIcon = Heroicon::CurrencyDollar;
    
    protected function canView(): bool
    {
        return auth()->user() && auth()->user()->role === 'admin';
    }

    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TabsWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }
}
