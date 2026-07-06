@extends('layouts.ferreteria')

@section('title', 'Marcas - Ferretería Guisella')

@section('content')
<div class="animate-fade-up">
    <h1 style="margin: 0;">Marcas</h1>
    <p class="subtitle">Explora nuestros productos por fabricante</p>
</div>

@if($marcas->isEmpty())
    <div style="text-align: center; padding: 60px 20px; color: var(--muted);">
        <p>No hay marcas disponibles en este momento.</p>
    </div>
@else
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 30px;">
        @foreach($marcas as $marca)
            <a href="{{ route('marcas.productos', $marca->id) }}"
               style="display: flex; align-items: center; justify-content: center; background: white; border-radius: 16px; border: 1px solid var(--border); padding: 30px 20px; text-decoration: none; transition: box-shadow 0.2s, transform 0.2s; min-height: 120px;"
               onmouseover="this.style.boxShadow='0 8px 24px rgba(0,0,0,0.12)';this.style.transform='translateY(-4px)'"
               onmouseout="this.style.boxShadow='none';this.style.transform='none'">
                @if($marca->logo)
                    <img src="{{ asset('storage/' . $marca->logo) }}"
                         alt="{{ $marca->nombre }}"
                         style="max-height: 70px; max-width: 160px; object-fit: contain;">
                @else
                    <span style="font-size: 1.3rem; font-weight: 800; color: var(--text-main); text-align: center;">
                        {{ $marca->nombre }}
                    </span>
                @endif
            </a>
        @endforeach
    </div>
@endif
@endsection
