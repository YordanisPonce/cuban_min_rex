<?php

namespace App\Filament\Pages;

use App\Livewire\LiquidationsTableWidget;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;

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
            LiquidationsTableWidget::class
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }
}
