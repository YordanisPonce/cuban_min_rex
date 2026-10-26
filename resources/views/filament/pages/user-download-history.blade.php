<x-filament-panels::page>
    <livewire:user-downloads-table :userId="$this->user?->id" key="user-downloads-{{ $this->user?->id }}" />
</x-filament-panels::page>
