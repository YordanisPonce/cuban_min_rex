<div>
    <x-filament::tabs label="Content tabs">
        <x-filament::tabs.item wire:click="setActiveTab('1')" :active="$activeTab === '1'" style="width: 50%;">
            Ventas
        </x-filament::tabs.item>

        <x-filament::tabs.item wire:click="setActiveTab('2')" :active="$activeTab === '2'" style="width: 50%;">
            Subscripciones
        </x-filament::tabs.item>
    </x-filament::tabs>

    @if ($activeTab === '1')
        <div style="margin-top: 1rem;">
            @livewire($tabs[0]['content'])
        </div>
    @elseif ($activeTab === '2')
        <div style="margin-top: 1rem;">
            @livewire($tabs[1]['content'])
        </div>
    @endif
</div>