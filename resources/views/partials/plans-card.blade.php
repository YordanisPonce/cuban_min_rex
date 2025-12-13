@php
    $isActive =
        auth()->check() &&
        auth()->user()->current_plan_id === $plan->id &&
        auth()->user()->hasActivePlan();
@endphp
<div class="pricing-card">
    <spam class="type">{{$plan->name}}</spam>
    <div class="price" data-content="${{$plan->price}}"><span>$</span>{{$plan->price}}</div>
    <h5 class="plan">plan</h5>
    <div class="details mb-5">
        <p>Duración: {{$plan->duration_months}} {{$plan->duration_months > 1 ? 'meses' : 'mes'}}</p>
        <p>Descargas por archivo: {{$plan->downloads}}</p>
        @if ($plan->features)
            @foreach ($plan->features as $item)
                <p>{{ $item['value'] }}</p>
            @endforeach
        @endif
    </div>
    @if ($isActive)
    <div class="buy-button active">
        <h3 class="btn"><a style="color: gray">Ya lo tienes</a></h3>
    </div>
    @else
    <a  href="{{ route('payment.form', $plan->id) }}" class="buy-button">
        <h3 class="btn">Adquirir</h3>
    </a>
    @endif
</div>