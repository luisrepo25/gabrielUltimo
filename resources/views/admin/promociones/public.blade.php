@extends('layouts.ferreteria')

@section('title', 'Promociones - Ferretería Guisella')

@section('content')
<div class="animate-fade-up" style="display: flex; flex-direction: column; gap: 24px;">

    <div style="text-align: center; margin-bottom: 8px;">
        <h1 style="margin: 0; font-size: 2.2rem; color: #1F2937;">🎉 Promociones Activas</h1>
        <p class="subtitle" style="margin: 8px 0 0 0; font-size: 1rem;">¡Aprovecha nuestras ofertas especiales y ahorra en tus compras!</p>
    </div>

    @if($promociones->count() > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px;">
            @foreach($promociones as $promo)
                <div style="background: white; border-radius: 16px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.05); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 32px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(0,0,0,0.05)';">

                    <!-- Banner -->
                    <div style="padding: 20px; background: {{ $promo->tipo === 'Combo' ? 'linear-gradient(135deg, #7C3AED, #A855F7)' : 'linear-gradient(135deg, #00AF9A, #06D6A0)' }}; color: white; text-align: center;">
                        @if($promo->tipo === 'Global')
                            <p style="margin: 0; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.9;">Descuento Especial</p>
                            <p style="margin: 4px 0 0 0; font-size: 2.5rem; font-weight: 900;">{{ number_format($promo->descuento_porcentaje, 0) }}% OFF</p>
                        @else
                            <p style="margin: 0; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.9;">Combo Especial</p>
                            <p style="margin: 4px 0 0 0; font-size: 2.5rem; font-weight: 900;">{{ number_format($promo->precio_combo, 2) }} Bs.</p>
                        @endif
                    </div>

                    <div style="padding: 20px;">
                        <h3 style="margin: 0 0 6px 0; font-size: 1.2rem; color: #1F2937;">{{ $promo->nombre }}</h3>
                        @if($promo->descripcion)
                            <p style="margin: 0 0 16px 0; font-size: 0.9rem; color: var(--text-light); line-height: 1.5;">{{ $promo->descripcion }}</p>
                        @endif

                        <p style="margin: 0 0 8px 0; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--text-light);">Productos incluidos:</p>
                        <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 16px;">
                            @foreach($promo->productos as $prod)
                                <span style="background: #F3F4F6; color: #374151; font-size: 0.8rem; padding: 4px 10px; border-radius: 6px; font-weight: 600;">{{ $prod->nombre }}</span>
                            @endforeach
                        </div>

                        <div style="padding-top: 12px; border-top: 1px solid var(--border); font-size: 0.8rem; color: var(--text-light);">
                            ⏰ Válido hasta el {{ $promo->fecha_fin->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 60px 20px; color: var(--text-light);">
            <p style="font-size: 3rem; margin: 0;">🛒</p>
            <h3 style="margin: 12px 0 4px 0; font-size: 1.3rem; color: #374151;">No hay promociones activas</h3>
            <p style="margin: 0; font-size: 0.95rem;">Vuelve pronto para ver nuestras ofertas especiales.</p>
        </div>
    @endif
</div>
@endsection
