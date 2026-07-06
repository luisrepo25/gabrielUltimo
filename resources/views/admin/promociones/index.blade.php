@extends('layouts.ferreteria')

@section('title', 'Promociones y Combos - Ferretería Guisella')

@section('content')
<div class="animate-fade-up" style="display: flex; flex-direction: column; gap: 24px;">

    <!-- HEADER -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="margin: 0; font-size: 2rem;">Promociones y Combos</h1>
            <p class="subtitle" style="margin: 4px 0 0 0;">Crea descuentos globales y combos especiales para tus clientes.</p>
        </div>
        <a href="{{ route('admin.promociones.create') }}" class="btn-productos" style="display: flex; align-items: center; gap: 8px; font-weight: 700; text-decoration: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
            Nueva Promoción
        </a>
    </div>

    <!-- MENSAJES -->
    @if (session('success'))
        <div class="alert alert-success" style="margin: 0;">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error" style="margin: 0;">{{ session('error') }}</div>
    @endif

    <!-- GRID DE PROMOCIONES -->
    @if($promociones->count() > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 20px;">
            @foreach($promociones as $promo)
                <div class="card" style="background: white; border-radius: 12px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.08)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.03)';">

                    <!-- Banner de Tipo -->
                    <div style="padding: 12px 20px; background: {{ $promo->tipo === 'Combo' ? 'linear-gradient(135deg, #7C3AED, #A855F7)' : 'linear-gradient(135deg, #00AF9A, #06D6A0)' }}; color: white;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 800; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                {{ $promo->tipo === 'Combo' ? '🎁 Combo' : '🏷️ Descuento Global' }}
                            </span>
                            @if($promo->estado === 'Activo')
                                <span style="background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 700;">ACTIVO</span>
                            @elseif($promo->estado === 'Expirado')
                                <span style="background: rgba(0,0,0,0.2); padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 700;">EXPIRADO</span>
                            @else
                                <span style="background: rgba(0,0,0,0.2); padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 700;">INACTIVO</span>
                            @endif
                        </div>
                    </div>

                    <div style="padding: 20px;">
                        <h3 style="margin: 0 0 4px 0; font-size: 1.15rem; color: #1F2937;">{{ $promo->nombre }}</h3>
                        @if($promo->descripcion)
                            <p style="margin: 0 0 12px 0; font-size: 0.85rem; color: var(--text-light); line-height: 1.4;">{{ $promo->descripcion }}</p>
                        @endif

                        <!-- Descuento o Precio Combo -->
                        <div style="margin-bottom: 12px;">
                            @if($promo->tipo === 'Global')
                                <span style="font-size: 1.8rem; font-weight: 900; color: #00AF9A;">{{ number_format($promo->descuento_porcentaje, 0) }}%</span>
                                <span style="font-size: 0.85rem; color: var(--text-light); font-weight: 600;"> de descuento</span>
                            @else
                                <span style="font-size: 1.8rem; font-weight: 900; color: #7C3AED;">{{ number_format($promo->precio_combo, 2) }} Bs.</span>
                                <span style="font-size: 0.85rem; color: var(--text-light); font-weight: 600;"> precio combo</span>
                            @endif
                        </div>

                        <!-- Productos -->
                        <div style="margin-bottom: 12px;">
                            <p style="margin: 0 0 6px 0; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--text-light);">Productos incluidos</p>
                            <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                @foreach($promo->productos->take(4) as $prod)
                                    <span style="background: #F3F4F6; color: #374151; font-size: 0.75rem; padding: 3px 8px; border-radius: 4px; font-weight: 600;">{{ $prod->nombre }}</span>
                                @endforeach
                                @if($promo->productos->count() > 4)
                                    <span style="background: #F3F4F6; color: var(--text-light); font-size: 0.75rem; padding: 3px 8px; border-radius: 4px; font-weight: 600;">+{{ $promo->productos->count() - 4 }} más</span>
                                @endif
                            </div>
                        </div>

                        <!-- Fechas -->
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 12px; border-top: 1px solid var(--border);">
                            <span style="font-size: 0.75rem; color: var(--text-light);">
                                📅 {{ $promo->fecha_inicio->format('d/m/Y') }} — {{ $promo->fecha_fin->format('d/m/Y') }}
                            </span>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('admin.promociones.edit', $promo->id) }}" style="color: #00AF9A; font-weight: 700; text-decoration: none; font-size: 0.85rem;">Editar</a>
                                <form action="{{ route('admin.promociones.destroy', $promo->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar esta promoción?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: #EF4444; font-weight: 700; cursor: pointer; font-size: 0.85rem;">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div style="margin-top: 20px;">{{ $promociones->links() }}</div>
    @else
        <div class="card" style="background: white; border-radius: 12px; border: 1px solid var(--border); padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
            <div style="text-align: center; padding: 50px 20px; color: var(--text-light); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 16px;">
                <div style="background: rgba(124, 58, 237, 0.08); width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #7C3AED;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                </div>
                <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800; color: #374151;">Sin promociones activas</h3>
                <p style="margin: 0; font-size: 0.9rem; max-width: 320px; line-height: 1.5;">Crea tu primera promoción o combo desde el botón superior.</p>
            </div>
        </div>
    @endif
</div>
@endsection
