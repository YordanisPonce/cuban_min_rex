<x-filament-panels::page>
    <x-filament::card>
        <div style='text-align: justify; display:flex; flex-direction: column; gap:10px;'>
            <h1 style='font-weight:700; font-size: x-large; display:flex; gap: 10px; align-items: center;'><x-filament::icon icon="heroicon-o-information-circle" />Comisión por descargas</h1>
            <p>La comisión por descargas de una suscripción no es un valor fijo, este se ve afectado por la cantidad de descargas que el usuario realizó sobre tus archivos con respecto a las descargas totales realizadas por dicho usuario durante el período de suscripción. </p>
            <p>Si un usuario descarga 10 canciones, y las 10 son tuyas. Tu comisión sería del 100% a repartir.  </p>
            <p>Pero si el usuario realizó 100 descargas y solo 10 son tuyas, tu comisión será del 10%.  </p>
            <p>Por eso este valor nunca es estático y siempre está en constante variación.</p>
            <p>Descargas repetidas de un mismo usuario sobre un mismo archivo no se tendrán en cuenta.</p>
        </div>
    </x-filament::card>

    <div style="margin-top: 20px;">

        <div style="margin-top: 20px; margin-bottom: 20px;">
            {{ $this->table }}
        </div>

        <x-filament-actions::modals />
    </div>
</x-filament-panels::page>
