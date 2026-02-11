<div style="margin-top: 20px;">
    <form wire:submit="save">
        {{ $this->form }}

        <div style="margin-top: 20px;">
            @foreach ($this->getFormActions() as $action)
                {{ $action }}
            @endforeach
        </div>
    </form>

     <div style="margin-top: 20px; margin-bottom: 20px;">
        {{ $this->table }}
    </div>

    <x-filament-actions::modals />
</div>
