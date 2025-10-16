<x-filament-panels::page>
    <livewire:user-payments-table :userId="$this->user?->id" key="user-payments-{{ $this->user?->id }}" />
</x-filament-panels::page>
