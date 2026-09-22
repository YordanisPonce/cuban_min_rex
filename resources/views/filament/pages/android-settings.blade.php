<div style="margin-top: 20px;">
    <form wire:submit="save"  style="width: 100%; margin: auto;">
        {{ $this->form }}

        <div style="margin-top: 20px;">
            @foreach ($this->getFormActions() as $action)
                {{ $action }}
            @endforeach
        </div>
    </form>
    <x-filament-actions::modals />
</div>
