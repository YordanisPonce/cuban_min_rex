<x-filament-panels::page>
    <x-filament::card>
        <div style="display: flex; border-bottom: 1px solid black; border-top: 1px solid black; padding: 0.5rem; text-align: center">
            <strong style="width: 20%">FECHA</strong>
            <strong style="width: 25%">CANTIDAD</strong>
            <strong style="width: 25%">MONEDA</strong>
            <strong style="width: 30%">TRANSACCIÓN</strong>
        </div>
        @foreach ($user->payments as $payment)
        <div style="display: flex; border-bottom: 1px solid grey; padding: 0.5rem; text-align: center; cursor: pointer" onmouseenter="this.style.backgroundColor = '#ccc'" onmouseleave="this.style.backgroundColor = '#fff'">
            <spam style="width: 20%; text-overflow: ellipsis; white-space: nowrap; display: block; overflow: hidden">{{$payment->created_at}}</spam>
            <spam style="width: 25%; text-overflow: ellipsis; white-space: nowrap; display: block; overflow: hidden">{{$payment->amount}}</spam>
            <spam style="width: 25%; text-overflow: ellipsis; white-space: nowrap; display: block; overflow: hidden">{{$payment->currency}}</spam>
            <spam style="width: 30%; text-overflow: ellipsis; white-space: nowrap; display: block; overflow: hidden">{{$payment->paypal_responce['batch_header']['payout_batch_id'] ?? 'No definido'}}</spam>
        </div>
        @endforeach
    </x-filament::card>
</x-filament-panels::page>