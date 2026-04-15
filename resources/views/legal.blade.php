@extends('layouts.app')

@section('title', $title.' - ' . config('app.name'))

@push('styles')
    <style>
        .legal-container {
            width: 100%;
            max-width: 1400px;
            margin: 100px auto;
            padding: 0 20px;
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            text-align: center;
        }
        p {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }
    </style>
@endpush

@section('content')
    <div class="legal-container">
        <h1>{{ $title }}</h1>
        <div>
            {!! $text !!}
        </div>
    </div>
@endsection