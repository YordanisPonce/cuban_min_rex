<div style="margin-top: 20px;">
    <form wire:submit="save"  style="width: 100%; margin: auto;">
        {{ $this->form }}

        <div style="margin: 10px; color: red; display: flex; align-items: center; gap: 10px;">
            <x-icon name="heroicon-o-exclamation-triangle" style="max-width: 40px; max-height: 40px;"/>
            <p>Desactivar una forma de pago no cancela las suscripciones existentes creadas mediante esa forma de pago, las debe cancelar manualmente mediante la plataforma correspondiente.</p>
        </div>

        <div style="margin-top: 20px;">
            @foreach ($this->getFormActions() as $action)
                {{ $action }}
            @endforeach
        </div>
    </form>

    <x-filament-actions::modals />
</div>
