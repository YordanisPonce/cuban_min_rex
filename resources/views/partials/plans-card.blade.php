@php
    $isActive =
        auth()->check() &&
        auth()->user()->current_plan_id === $plan->id &&
        auth()->user()->hasActivePlan();
@endphp
<div class="plan-card">
    <span class="plan-badge"></span>
    <div id="icon"></div>
    <div class="plan-name">{{$plan->name}}</div>
    <div class="plan-price"><sup>$</sup>{{$plan->price}}</div>
    <div class="plan-period">POR MES</div>
    <ul class="plan-features">
        <li><i class="fas fa-check"></i><span><strong>Límite de descargas: {{ $plan->downloads }}</strong></span></li>
        @if ($plan->features)
            @foreach ($plan->features as $item)
                <li><i class="fas fa-check"></i><span><strong>{{ $item['value']}}</strong></span></li>
            @endforeach
        @endif
    </ul>
    @if ($isActive)
        <button class="plan-btn filled">ACTUAL</button>
    @else
    <a  href="{{ route('payment.form', str_replace(' ', '_', $plan->name)) }}" class="plan-btn filled">
        ESCOGER PLAN
    </a>
    @endif
</div>