@extends('layouts.ferreteria')

@section('title', 'Catálogo de Productos - Ferretería Guisella')

@section('content')
    {{-- ═══════════════════════════════════════════════════
         HERO SLIDER DE PROMOCIONES Y COMBOS
         ═══════════════════════════════════════════════════ --}}
    @if(isset($promociones) && $promociones->count() > 0)
        <div class="promo-hero-slider" id="promoSlider">
            @foreach($promociones as $index => $promo)
                <div class="promo-slide {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">
                    {{-- Background image (custom URL or placeholder mockups) --}}
                    <div class="promo-slide-bg" style="background-image: url('{{ $promo->imagen ?: 'https://images.unsplash.com/photo-' . ($promo->tipo === 'Combo' ? '1581783898377-1c85bf937427' : '1504148455328-c376907d081c') . '?w=1400&h=500&fit=crop&auto=format&q=80' }}'); background-color: #0A1128;"></div>

                    <div class="promo-slide-content">
                        {{-- Badge de tipo --}}
                        <span class="promo-badge-tipo {{ strtolower($promo->tipo) }}">
                            @if($promo->tipo === 'Global')
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 12 20 22 4 22 4 12"></polyline><rect x="2" y="7" width="20" height="5"></rect><line x1="12" y1="22" x2="12" y2="7"></line><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg>
                                Oferta Especial
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                                Combo Exclusivo
                            @endif
                        </span>

                        {{-- Título --}}
                        <h2 class="promo-slide-title">{{ $promo->nombre }}</h2>

                        {{-- Precio / Descuento destacado --}}
                        @if($promo->tipo === 'Global')
                            <div class="promo-slide-discount">{{ number_format($promo->descuento_porcentaje, 0) }}% OFF</div>
                        @else
                            <div class="promo-slide-discount">{{ number_format($promo->precio_combo, 2) }} Bs.</div>
                        @endif

                        {{-- Descripción --}}
                        @if($promo->descripcion)
                            <p class="promo-slide-desc">{{ Str::limit($promo->descripcion, 120) }}</p>
                        @endif

                        {{-- Botón de compra directa --}}
                        <form action="{{ route('promocion.comprar', $promo->id) }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="promo-slide-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                                Comprar Ahora
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach

            {{-- Dots de paginación --}}
            <div class="promo-dots">
                @foreach($promociones as $index => $promo)
                    <div class="promo-dot {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}"></div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="animate-fade-up">
        <h1 style="margin: 0;">Catálogo de Productos</h1>
        <p class="subtitle">Explora nuestro inventario disponible</p>
    </div>

    <div class="catalog-container">
        @foreach($categorias as $categoria)
            @include('inventario.categoria-recursiva', ['categoria' => $categoria])
        @endforeach
    </div>

    <div style="margin-top: 40px; padding: 30px; background: white; border-radius: 20px; text-align: center; border: 1px dashed #cbd5e1;">
        @guest
            <p style="color: var(--muted);">¿Necesitas finalizar un pedido o cotización? <a href="{{ route('login') }}" style="color: var(--primary); font-weight: bold;">Inicia sesión</a> para proceder al pago.</p>
        @else
            <p style="color: var(--muted);">¿Listo para pedir? <a href="{{ route('carrito.index') }}" style="color: var(--primary); font-weight: bold;">Revisa tu carrito</a> y procede al pago.</p>
        @endguest
    </div>
@endsection

@push('scripts')
<script>
    (function() {
        const slider = document.getElementById('promoSlider');
        if (!slider) return;

        const slides = slider.querySelectorAll('.promo-slide');
        const dots = slider.querySelectorAll('.promo-dot');
        if (slides.length <= 1) return;

        let currentIndex = 0;
        let autoPlayInterval = null;

        function goToSlide(index) {
            // Remove active from all
            slides.forEach(s => s.classList.remove('active'));
            dots.forEach(d => d.classList.remove('active'));

            // Set new active
            currentIndex = index;
            slides[currentIndex].classList.add('active');
            dots[currentIndex].classList.add('active');
        }

        function nextSlide() {
            goToSlide((currentIndex + 1) % slides.length);
        }

        function startAutoPlay() {
            if (autoPlayInterval) clearInterval(autoPlayInterval);
            autoPlayInterval = setInterval(nextSlide, 3000);
        }

        // Click on dots
        dots.forEach((dot, index) => {
            dot.addEventListener('click', function() {
                goToSlide(index);
                startAutoPlay(); // Reset timer on manual navigation
            });
        });

        // Start auto-play
        startAutoPlay();

        // Pause on hover
        slider.addEventListener('mouseenter', () => {
            if (autoPlayInterval) clearInterval(autoPlayInterval);
        });
        slider.addEventListener('mouseleave', () => {
            startAutoPlay();
        });
    })();
</script>
@endpush
