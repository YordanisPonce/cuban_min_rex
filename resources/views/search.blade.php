@extends('layouts.app')
@php
    use Carbon\Carbon;
    Carbon::setLocale('es');
@endphp
@section('title', 'Página de Resultados de Busqueda')

@push('styles')
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page.css') }}" />
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/front-page-payment.css') }}" />
@endpush

@section('content')
    <!-- FAQ: Start -->
    <section id="musicSearch" class="section-py bg-body" style="height: 100vh;">
        <div class="container" style="margin-top: 60px;">
            <div class="text-center mb-4">
                <span class="badge bg-label-primary">🎶 Archivos Disponibles</span>
            </div>
            <div class="card">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatables-basic table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Fecha</th>
                                <th>Subido por</th>
                                <th>Nombre</th>
                                <th>Álbum</th>
                                <th>Categoría</th>
                                @auth
                                    @if (!Auth::user()->hasActivePlan())
                                        <td></td>
                                    @endif
                                @else
                                    <th></th>
                                @endauth
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($results as $file)
                                @php
                                    $date = Carbon::parse($file['date'])->translatedFormat('d \d\e F \d\e Y H:i');
                                @endphp
                                <tr>
                                    <td></td>
                                    <td>{{ $date }}</td>
                                    <td>{{ $file['user'] }}</td>
                                    <td>{{ $file['name'] }}</td>
                                    <td>{{ $file['collection'] }}</td>
                                    <td>{{ $file['category'] }}</td>
                                    @auth
                                        @if (!Auth::user()->hasActivePlan())
                                            <td>$ {{ $file['price'] }}</td>
                                        @endif
                                        <td class="d-flex gap-2">
                                            @if (Auth::user()->hasActivePlan())
                                                <a style="display: flex; width: 20px"
                                                    href="{{ route('file.download', $file['id'])}}">{{ svg('entypo-download') }}</a>
                                            @else
                                                <a style="display: flex; width: 20px" data-url="{{route('file.pay', $file['id']) }}"  onclick="proccessPayment(this.dataset.url)">{{ svg('vaadin-cart') }}</a>
                                            @endif
                                        </td>
                                    @else
                                        <td>$ {{ $file['price'] }}</td>
                                        <td class="d-flex gap-2">
                                            <a style="display: flex; width: 20px" data-url="{{route('file.pay', $file['id']) }}"  onclick="proccessPayment(this.dataset.url)">{{ svg('vaadin-cart') }}</a>
                                            
                                        </td>
                                    @endauth
                                    <td>
                                        @if (!$file['isZip'])
                                        <a style="display: flex; width: 20px" class="cursor-pointer" data-url="{{$file['url']}}" data-state="pause" onclick="playAudio(this)"
                                                >{{ svg('vaadin-play') }}</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($results->isEmpty())
                <h4 class="text-center text-primary mt-2">Sin resultados</h4>
            @endif
        </div>
    </section><!-- FAQ: End -->
@endsection

@push('scripts')
<script>
    let currentAudio = null;

    function stopCurrentAudio() {
        if (currentAudio) {
            currentAudio.pause();
            currentAudio.currentTime = 0;
            currentAudio = null;
        }
    }

    function playAudio(element){
        let audio = document.createElement('audio');

        let binaryData = element.dataset.url;

        let byteCharacters = atob(binaryData);
        let byteNumbers = new Array(byteCharacters.length);
        for (let i = 0; i < byteCharacters.length; i++) {
            byteNumbers[i] = byteCharacters.charCodeAt(i);
        }
        let byteArray = new Uint8Array(byteNumbers);
        let blob = new Blob([byteArray], { type: 'audio/mpeg' });
        let url = URL.createObjectURL(blob);
        
        audio.src = url;

        if(element.dataset.state == "pause"){
            stopCurrentAudio();
            currentAudio = audio;
            audio.play();
            element.innerHTML = '{{ svg('vaadin-pause') }}';
            element.dataset.state = "play";
        } else {
            element.innerHTML = '{{ svg('vaadin-play') }}';
            stopCurrentAudio();
            element.dataset.state = "pause";
        }
        
        audio.addEventListener('ended', () => {
            element.innerHTML = '{{ svg('vaadin-play') }}';
            element.dataset.state = "pause";
        });
    }

    function proccessPayment(rute) {
        Swal.fire({
            title: '¿Proceder con el pago?',
            text: "Serás redirigido a Stripe para completar tu pago.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector('#loader').style.display = 'flex';
                fetch(rute)
                    .then(async res => {
                        let data;
                        
                        try {
                            data = await res.json();
                        } catch {
                            document.querySelector('#loader').style.display = 'none';
                            throw new Error("Respuesta inesperada del servidor");
                        }

                        if (res.ok && data.url) {
                            window.location.href = data.url;
                        } else {
                            document.querySelector('#loader').style.display = 'none';
                            Swal.fire("Error", data.error ?? "No se pudo generar la sesión de pago", "error");
                        }
                    })
                    .catch(err => {
                        document.querySelector('#loader').style.display = 'none';
                        Swal.fire("Error", err.message, "error");
                    });
            }
        });
    }
</script>
@endpush