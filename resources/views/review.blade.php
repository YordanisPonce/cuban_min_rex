@extends('layouts.app')
@php
    use Carbon\Carbon;
    Carbon::setLocale('es');
    $success = session('success');
    $error = session('error');
@endphp
@section('title', 'Review - ' . config('app.name'))

@push('styles')
@endpush

@section('content')
    <div class="container reviews-container">
        <div class="left-side">
            <h2>Reseñas recientes:</h2>
            <div style="width: 100%; max-width: 600px; display: flex; flex-direction: column; gap: 15px;">
                @foreach ($reviews as $review)
                    <div class="review-item">
                        <div class="review-header">
                            <strong>{{ $review->user->name }}</strong>
                            <span>{{ Carbon::parse($review->created_at)->diffForHumans() }}</span>
                        </div>
                        <div class="rating">
                            @for ($i = 1; $i <= 5; $i++)
                                <i
                                    class="{{ $review->rating < $i && $review->rating >= $i - 0.5 ? 'fa-solid fa-star-half-stroke filled' : ($review->rating >= $i ? 'fa-solid fa-star filled' : 'fa-regular fa-star') }}"></i>
                            @endfor
                        </div>
                        <div>
                            <p>{{ $review->comment }}</p>
                        </div>
                    </div>
                @endforeach
                @if ($reviews->count() === 0)
                    <p>No hay reseñas, se el primero en dar su opinión.</p>
                @endif
            </div>
        </div>
        <div class="right-side">
            <h2>Deja tu reseña: </h2>
            <form class="review-form" method="POST" action="{{ route('submit.review') }}">
                @csrf
                <div class="form-rating">
                    <div class="rating" id="rating">
                        <i class="{{ $review ? ($review->rating < 1 && $review->rating >= 0.5 ? 'fa-solid fa-star-half-stroke' : ($review->rating >= 1 ? 'fa-solid fa-star' : 'fa-regular fa-star')) : 'fa-regular fa-star' }} {{ $review && $review->rating >= 0.5 ? 'filled' : '' }}"
                            data-index="1"></i>
                        <i class="{{ $review ? ($review->rating < 2 && $review->rating >= 1.5 ? 'fa-solid fa-star-half-stroke' : ($review->rating >= 2 ? 'fa-solid fa-star' : 'fa-regular fa-star')) : 'fa-regular fa-star' }} {{ $review && $review->rating >= 1.5 ? 'filled' : '' }}"
                            data-index="2"></i>
                        <i class="{{ $review ? ($review->rating < 3 && $review->rating >= 2.5 ? 'fa-solid fa-star-half-stroke' : ($review->rating >= 3 ? 'fa-solid fa-star' : 'fa-regular fa-star')) : 'fa-regular fa-star' }} {{ $review && $review->rating >= 2.5 ? 'filled' : '' }}"
                            data-index="3"></i>
                        <i class="{{ $review ? ($review->rating < 4 && $review->rating >= 3.5 ? 'fa-solid fa-star-half-stroke' : ($review->rating >= 4 ? 'fa-solid fa-star' : 'fa-regular fa-star')) : 'fa-regular fa-star' }} {{ $review && $review->rating >= 3.5 ? 'filled' : '' }}"
                            data-index="4"></i>
                        <i class="{{ $review ? ($review->rating < 5 && $review->rating >= 4.5 ? 'fa-solid fa-star-half-stroke' : ($review->rating >= 5 ? 'fa-solid fa-star' : 'fa-regular fa-star')) : 'fa-regular fa-star' }} {{ $review && $review->rating >= 4.5 ? 'filled' : '' }}"
                            data-index="5"></i>
                    </div>
                    <input class="valor" id="valor" type="number" name="rating" min="0"
                        value="{{ $review ? $review->rating : 0 }}" max="5" step="0.5" readonly required>
                </div>
                <textarea class="comment-box" placeholder="Escribe tu comentario aquí..." name="comment" required>{{ $review ? $review->comment : '' }}</textarea>
                <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Enviar Reseña</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const stars = document.querySelectorAll('#rating i');
        const valor = document.getElementById('valor');
        let currentRating = valor.value;

        stars.forEach(star => {
            star.addEventListener('mousemove', (e) => {
                const index = parseInt(star.getAttribute('data-index'));
                const rect = star.getBoundingClientRect();
                const offsetX = e.clientX - rect.left;
                const half = offsetX < rect.width / 2; // mitad izquierda = media estrella
                let val = index - (half ? 0.5 : 0);
                fillStars(val + (half ? 0.5 : 0));
            });

            star.addEventListener('click', (e) => {
                const index = parseInt(star.getAttribute('data-index'));
                const rect = star.getBoundingClientRect();
                const offsetX = e.clientX - rect.left;
                const half = offsetX < rect.width / 2;
                currentRating = index - (half ? 0.5 : 0);
                valor.value = currentRating;
            });
        });

        document.getElementById('rating').addEventListener('mouseleave', () => {
            fillStars(currentRating);
        });

        function fillStars(val) {
            stars.forEach(star => {
                const index = parseInt(star.getAttribute('data-index'));
                star.className = "fa-regular fa-star"; // reset
                if (index <= val) {
                    star.className = "fa-solid fa-star filled";
                } else if (index - 0.5 === val) {
                    star.className = "fa-solid fa-star-half-stroke filled";
                }
            });
        }
    </script>
@endpush
