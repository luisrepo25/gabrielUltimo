@extends('layouts.ferreteria')

@section('title', $producto->nombre . ' - Detalles del Producto')

@section('content')
<div class="animate-fade-up" style="max-width: 900px; margin: 0 auto;">
    
    <div style="margin-bottom: 20px;">
        <a href="{{ url()->previous() == url()->current() ? url('/') : url()->previous() }}" class="btn-action" style="display: inline-flex; align-items: center; gap: 8px; background: var(--bg-light); color: var(--text-main); border: 1px solid var(--border);">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Volver
        </a>
    </div>

    <div class="card" style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; padding: 40px;">
        {{-- Lado Izquierdo: Imagen --}}
        <div style="background: var(--bg-light); border-radius: 16px; min-height: 350px; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
            @if($producto->imagen)
                <img src="{{ $producto->imagen }}" alt="{{ $producto->nombre }}" style="width: 100%; height: 100%; object-fit: cover;">
            @else
                <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="var(--border)" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            @endif
            @if($producto->cantidad <= 0)
                <span class="badge-pill" style="position: absolute; top: 20px; right: 20px; background: var(--danger); color: white;">Agotado</span>
            @endif
        </div>

        {{-- Lado Derecho: Detalles --}}
        <div>
            @if($producto->categoria)
                <span style="color: var(--primary); font-size: 0.9rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                    {{ $producto->categoria->nombre }}
                </span>
            @endif
            
            <h1 style="margin: 10px 0 15px; font-size: 2.2rem; color: var(--text-dark);">{{ $producto->nombre }}</h1>
            
            <div style="font-size: 2rem; font-weight: 900; color: var(--text-dark); margin-bottom: 20px;">
                {{ number_format($producto->precio, 2) }} Bs.
            </div>

            <p style="color: var(--text-muted); line-height: 1.6; margin-bottom: 30px; font-size: 1.05rem;">
                {{ $producto->descripcion ?? 'Este producto no cuenta con una descripción detallada en este momento.' }}
            </p>

            {{-- Características Técnicas --}}
            <div style="background: var(--bg-light); padding: 20px; border-radius: 12px; margin-bottom: 30px;">
                <h4 style="margin: 0 0 15px 0; font-size: 1rem; color: var(--text-main); border-bottom: 1px solid var(--border); padding-bottom: 10px;">Especificaciones</h4>
                <ul style="list-style: none; padding: 0; margin: 0; display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 0.95rem;">
                    
                    @if($producto->marca)
                    <li><strong style="color: var(--text-main);">Marca:</strong> <span style="color: var(--text-muted);">{{ $producto->marca->nombre }}</span></li>
                    @endif

                    <li><strong style="color: var(--text-main);">Stock:</strong> 
                        <span style="color: {{ $producto->cantidad > 0 ? 'var(--success)' : 'var(--danger)' }}; font-weight: bold;">
                            {{ $producto->cantidad }} unid.
                        </span>
                    </li>
                    
                    @if($producto->color)
                    <li><strong style="color: var(--text-main);">Color:</strong> <span style="color: var(--text-muted);">{{ $producto->color->nombre }}</span></li>
                    @endif
                    
                    @if($producto->medida)
                    <li>
                        <strong style="color: var(--text-main);">Medidas:</strong> 
                        <span style="color: var(--text-muted);">
                            {{ $producto->medida->longitud ?? '-' }}x{{ $producto->medida->ancho ?? '-' }}x{{ $producto->medida->alto ?? '-' }}
                        </span>
                    </li>
                    @endif
                    
                    @if($producto->volumen)
                    <li>
                        <strong style="color: var(--text-main);">Peso/Vol:</strong> 
                        <span style="color: var(--text-muted);">
                            {{ $producto->volumen->peso ?? '-' }} / {{ $producto->volumen->volumen_m3 ?? '-' }} m³
                        </span>
                    </li>
                    @endif
                </ul>
            </div>

            {{-- Acción de Compra --}}
            @if($producto->cantidad > 0)
                <form action="{{ route('carrito.add') }}" method="POST" class="ajax-cart-form" style="display: flex; gap: 15px; align-items: center;">
                    @csrf
                    <input type="hidden" name="idproducto" value="{{ $producto->idproducto }}">
                    
                    <div style="display: flex; align-items: center; border: 1px solid var(--border); border-radius: 8px; overflow: hidden; background: white;">
                        <span style="padding: 10px 15px; background: var(--bg-light); color: var(--text-main); font-weight: bold; border-right: 1px solid var(--border);">Cant.</span>
                        <input type="number" name="cantidad" value="1" min="1" max="{{ $producto->cantidad }}" style="border: none; padding: 10px; width: 80px; text-align: center; font-size: 1rem; font-weight: bold; outline: none;">
                    </div>

                    <button type="submit" class="btn-save" style="flex: 1; padding: 14px; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; gap: 10px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                        Añadir al carrito
                    </button>
                </form>
            @else
                <div style="background: var(--danger-light); color: var(--danger); padding: 15px; border-radius: 8px; text-align: center; font-weight: bold; border: 1px solid rgba(239, 68, 68, 0.2);">
                    Este producto se encuentra actualmente agotado.
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    /* Estilos adicionales para esta vista */
    @media (max-width: 768px) {
        .card {
            grid-template-columns: 1fr !important;
            padding: 20px !important;
            gap: 20px !important;
        }
        .card > div:first-child {
            min-height: 250px !important;
        }
    }
</style>
@endsection
