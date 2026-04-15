<x-filament-panels::page>
    <form wire:submit="save"  style="width: 100%; margin: auto;">
        {{ $this->form }}

        <div style="margin-top: 20px;">
            @foreach ($this->getFormActions() as $action)
                {{ $action }}
            @endforeach
        </div>
    </form> 

    <x-filament::card style="width: 250px; height:140px;">
        <h2 style="font-size: 1rem; margin-bottom: 0.5rem">Preview del Logo</h2>
        <img src="{{ config('app.logo') }}"  style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; flex-shrink: 0;"/>
    </x-filament::card>

    <x-filament-actions::modals />
</x-filament-panels::page>
