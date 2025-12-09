@php
    use App\Models\Cart;
@endphp
<div class="{{ isset($top) ? 'card mb-1 px-0' : 'card mb-1 mb-md-0 px-0' }}">
    <div class="row g-0">
        <div style="width: 10%;">
            <img class="card-img card-img-left" src="{{ $item->poster ? $item->poster : ($item->user->photo ? $item->user->photo : config('app.logo')) }}" alt="{{ $item->name }}" />
        </div>
        @isset($top)
        <div style="width: 10%;">
            <div class="d-flex justify-content-center align-items-center h-100">
                <p class="text-secondary" style="font-weight: 800; font-size:xx-large">{{ $top }}</p>
            </div>
        </div>
        @endisset()
        <div style="{{ isset($top) ? 'width: 70%;' : 'width: 80%;' }}">
            <div class="p-2">
                <h5 class="card-title mb-0 text-truncate">{{ $item->name}}</h5>
                <p class="card-text mb-0 text-truncate" style="color: var(--bs-primary)">{{ $item->user ? $item->user->name : 'Desconocido' }}</p>
                <p class="card-text d-flex gap-4">
                    <small class="text-body-secondary">{{ $item->category ? $item->category->name : 'Sin Categoría' }}</small>
                    @isset($top)
                    <small class="text-body-secondary d-flex gap-2">
                        <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 15C3 17.8284 3 19.2426 3.87868 20.1213C4.75736 21 6.17157 21 9 21H15C17.8284 21 19.2426 21 20.1213 20.1213C21 19.2426 21 17.8284 21 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 3V16M12 16L16 11.625M12 16L8 11.625" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg> 
                    {{ $item->download_count }}</small>
                    @endisset
                </p>
            </div>
        </div>
        <div class="d-flex flex-column justify-content-center align-items-center" style="width: 10%; border-left: 2px solid black">
            <div class="w-100 d-flex justify-content-center align-items-center" style="height: 38px;">
                <a id="{{$item->id}}" style="display: flex;" class="btn btn-icon rounded-pill play-button cursor-pointer" data-rute="{{ route('file.play', [$item->collection ?? 'none', $item->id])}}" data-status="off" onclick="playAudio(this)"
                    ><i class="icon-base ti tabler-player-play-filled"></i></a>
            </div>
            <div class="dropdown d-flex justify-content-center">
                <button class="btn btn-text-secondary btn-icon rounded-pill text-body-secondary border-0" type="button" id="BulkOptions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="icon-base ti tabler-dots icon-22px text-body-secondary"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="BulkOptions">
                    @if ((Auth::user() && Auth::user()->hasActivePlan()) || !($item->price > 0))
                    <a class="dropdown-item d-flex gap-2" href="{{ route('file.download', $item->id)}}">
                        <i style="width: 20px">{{ svg('entypo-download') }}</i>
                        Descargar
                    </a>
                    @else
                        <p class="pt-2 text-center">Precio: $ {{ $item->price }}</p>
                        @if (!in_array($item->id,Cart::get_current_cart()->items ?? []))
                        <a class="dropdown-item d-flex gap-2" href="{{ route('file.add.cart', $item->id)}}">
                            <i style="width: 20px">
                                <svg fill="currentColor" width="auto" height="auto" viewBox="0 0 56 56" xmlns="http://www.w3.org/2000/svg"><path d="M 45.4157 28.7296 C 51.1548 28.7296 56 23.9261 56 18.1659 C 56 12.3642 51.2174 7.6022 45.4157 7.6022 C 39.6349 7.6022 34.8519 12.3642 34.8519 18.1659 C 34.8519 23.9677 39.6349 28.7296 45.4157 28.7296 Z M 16.9061 42.0175 L 41.1736 42.0175 C 41.9844 42.0175 42.6914 41.3520 42.6914 40.4579 C 42.6914 39.5637 41.9844 38.8982 41.1736 38.8982 L 17.2596 38.8982 C 16.0743 38.8982 15.3673 38.0665 15.1593 36.7980 L 14.8266 34.6146 L 41.2153 34.6146 C 43.3779 34.6146 44.7918 33.6788 45.5196 32.0152 L 45.6861 31.5785 C 37.9919 31.5577 32.0031 25.6312 32.0031 18.1659 C 32.0031 17.5421 32.0446 16.9182 32.1278 16.2944 L 12.1649 16.2944 L 11.7698 13.6535 C 11.5203 12.0523 10.9796 11.2413 8.8586 11.2413 L 1.5388 11.2413 C .7070 11.2413 0 11.9691 0 12.8009 C 0 13.6535 .7070 14.3813 1.5388 14.3813 L 8.5674 14.3813 L 11.8946 37.2139 C 12.3312 40.1668 13.8909 42.0175 16.9061 42.0175 Z M 45.4366 25.0282 C 44.7088 25.0282 44.0640 24.5291 44.0640 23.7389 L 44.0640 19.4344 L 40.0923 19.4344 C 39.3853 19.4344 38.8236 18.8521 38.8236 18.1659 C 38.8236 17.4589 39.3853 16.8767 40.0923 16.8767 L 44.0640 16.8767 L 44.0640 12.5721 C 44.0640 11.7820 44.7088 11.3037 45.4366 11.3037 C 46.1644 11.3037 46.7879 11.7820 46.7879 12.5721 L 46.7879 16.8767 L 50.7600 16.8767 C 51.4670 16.8767 52.0492 17.4589 52.0492 18.1659 C 52.0492 18.8521 51.4670 19.4344 50.7600 19.4344 L 46.7879 19.4344 L 46.7879 23.7389 C 46.7879 24.5291 46.1644 25.0282 45.4366 25.0282 Z M 15.1801 48.7549 C 15.1801 50.6473 16.6565 52.1237 18.5489 52.1237 C 20.4204 52.1237 21.9176 50.6473 21.9176 48.7549 C 21.9176 46.8834 20.4204 45.3862 18.5489 45.3862 C 16.6565 45.3862 15.1801 46.8834 15.1801 48.7549 Z M 34.6024 48.7549 C 34.6024 50.6473 36.1204 52.1237 38.0127 52.1237 C 39.8844 52.1237 41.3814 50.6473 41.3814 48.7549 C 41.3814 46.8834 39.8844 45.3862 38.0127 45.3862 C 36.1204 45.3862 34.6024 46.8834 34.6024 48.7549 Z"/></svg>
                            </i>
                            Añadir al Carrito
                        </a>
                        @else
                        <a class="dropdown-item d-flex gap-2" href="{{ route('file.remove.cart', $item->id)}}">
                            <i style="width: 20px">
                                <svg fill="currentColor" width="auto" height="auto" viewBox="0 0 56 56" xmlns="http://www.w3.org/2000/svg"><path d="M 45.4157 28.7296 C 51.2174 28.7296 56 23.9677 56 18.1659 C 56 12.3642 51.2174 7.6022 45.4157 7.6022 C 39.6349 7.6022 34.8519 12.3642 34.8519 18.1659 C 34.8519 23.9677 39.6349 28.7296 45.4157 28.7296 Z M 16.9061 42.0175 L 41.1736 42.0175 C 41.9844 42.0175 42.6914 41.3520 42.6914 40.4579 C 42.6914 39.5637 41.9844 38.8982 41.1736 38.8982 L 17.2596 38.8982 C 16.0743 38.8982 15.3673 38.0665 15.1593 36.7980 L 14.8266 34.6146 L 41.2153 34.6146 C 43.3779 34.6146 44.7918 33.6788 45.5196 32.0152 L 45.6861 31.5785 C 37.9919 31.5577 32.0031 25.6312 32.0031 18.1659 C 32.0031 17.5421 32.0446 16.9182 32.1278 16.2944 L 12.1649 16.2944 L 11.7698 13.6535 C 11.5203 12.0523 10.9796 11.2413 8.8586 11.2413 L 1.5388 11.2413 C .7070 11.2413 0 11.9691 0 12.8009 C 0 13.6535 .7070 14.3813 1.5388 14.3813 L 8.5674 14.3813 L 11.8946 37.2139 C 12.3312 40.1668 13.8909 42.0175 16.9061 42.0175 Z M 40.0923 19.4344 C 39.3853 19.4344 38.8027 18.8314 38.8027 18.1659 C 38.8027 17.4797 39.3853 16.8975 40.0923 16.8975 L 50.7805 16.8975 C 51.4670 16.8975 52.0492 17.4797 52.0492 18.1659 C 52.0492 18.8314 51.4670 19.4344 50.7805 19.4344 Z M 15.1801 48.7549 C 15.1801 50.6473 16.6565 52.1237 18.5489 52.1237 C 20.4204 52.1237 21.9176 50.6473 21.9176 48.7549 C 21.9176 46.8834 20.4204 45.3862 18.5489 45.3862 C 16.6565 45.3862 15.1801 46.8834 15.1801 48.7549 Z M 34.6024 48.7549 C 34.6024 50.6473 36.1204 52.1237 38.0127 52.1237 C 39.8844 52.1237 41.3814 50.6473 41.3814 48.7549 C 41.3814 46.8834 39.8844 45.3862 38.0127 45.3862 C 36.1204 45.3862 34.6024 46.8834 34.6024 48.7549 Z"/></svg>
                            </i>
                            Quitar del Carrito
                        </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>