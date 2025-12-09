<?php

namespace App\Filament\Pages;

use App\Livewire\UserEarningsTable;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Earnings extends Page
{
    protected string $view = 'filament.pages.earnings';

    protected static ?string $title = 'Ganancias';
    
    protected static BackedEnum|string|null $navigationIcon = Heroicon::CurrencyDollar;

    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserEarningsTable::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }
}
