<x-filament-panels::page>
    <div class="space-y-6">
        @livewire(\App\Filament\Widgets\DevStatsWidget::class, [
            'month' => $this->month,
            'year' => $this->year
        ], key('stats-' . $this->getFilterKey()))

        <div class="filament-tables-container" style="margin-top: 20px;">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    {{ $this->table }}
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>