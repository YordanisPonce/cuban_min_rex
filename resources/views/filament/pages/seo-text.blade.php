<x-filament-panels::page>
    <x-filament::card>
        <div style='text-align: justify; display:flex; gap:10px;'>
            <x-filament::icon icon="heroicon-o-information-circle" /> Una vez que se hagan cambios en esta sección, se deve consultar con el desarrollador para aplicarlos a nivel de sistema.
        </div>
    </x-filament::card>

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
