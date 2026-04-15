<x-filament-panels::page>
    <style>
        /* PAGINATION */
        .pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            margin-top: 2.5rem;
        }

        .page-item {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(245, 166, 35, 1);
            border-radius: 6px;
            background: rgba(245, 166, 35, .1);
            color: var(--muted);
            font-size: .85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s;
            font-family: inherit;
        }

        .page-item:not(.disabled):hover {
            background: rgba(245, 166, 35, 1);
        }

        .page-item.active {
            color: var(--primary);
            background: rgba(245, 166, 35, 1);
        }

        .page-item.disabled {
            opacity: 0.5;
            cursor: default;
        }

        .page-item.active {
            cursor: default;
        }
    </style>
    <div style="display:flex; gap: 20px; flex-wrap: wrap">
        @foreach ($this->banners() as $b)
            <x-filament::card>
                @if ($b->active)
                    <x-filament::badge color="success">ACTIVO</x-filament::badge>
                @else
                    <x-filament::badge color="danger">INACTIVO</x-filament::badge>
                @endif
                <img src="{{ $b->image() }}"
                    style="
                    min-width: 250px;
                    max-width: 500px;
                    width:100%;
                    max-height: 500px;
                    aspect-ratio: 2;
                    margin: 6px auto;
                ">
                <div style="display:flex; gap: 20px; justify-content: space-between; margin-top: 20px">
                    @if ($b->active)
                        <x-filament::button icon="heroicon-o-x-circle" color="danger"
                            wire:click="toggleBanner({{ $b->id }})">DESACTIVAR</x-filament::button>
                    @else
                        <x-filament::button icon="heroicon-o-check-circle" color="success"
                            wire:click="toggleBanner({{ $b->id }})">ACTIVAR</x-filament::button>
                    @endif
                    <x-filament::button icon="heroicon-o-trash" color="danger"
                        wire:click="deleteBanner({{ $b->id }})" outlined>ELIMINAR</x-filament::button>
                </div>
            </x-filament::card>
        @endforeach
        @if ($this->banners()->count() === 0)
            <div
                style="
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
            ">
                Sin Banners. Agrega Banners para promocionar mejor tu contenido.
            </div>
        @endif
    </div>
    {{ $this->banners()->links() }}
</x-filament-panels::page>
