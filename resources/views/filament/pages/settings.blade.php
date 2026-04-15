<div style="margin-top: 20px;">
    <form wire:submit="save"  style="width: 100%; margin: auto;">
        {{ $this->form }}

        <div style="margin-top: 20px;">
            @foreach ($this->getFormActions() as $action)
                {{ $action }}
            @endforeach
        </div>
    </form>

    <div style="width: 100%; margin-top: 20px; margin-bottom: 20px;">
        {{ $this->table }}
    </div>

    <x-filament-actions::modals />
</div>
