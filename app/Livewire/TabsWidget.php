<?php

namespace App\Livewire;

use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

class TabsWidget extends Widget
{
    protected string $view = 'livewire.tabs-widget';

    public $activeTab = '1';

    public function render(): View
    {
        return view('livewire.tabs-widget', [
            'tabs' => $this->getTabs()
        ]);
    }

    protected function getTabs(): array
    {
        return [
            [
                'label' => 'Liquidaciones',
                'content' => LiquidationsTableWidget::class,
            ],
            [
                'label' => 'Suscripciones',
                'content' => SubscriptionLiquidationTable::class,
            ],
        ];
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }
}
