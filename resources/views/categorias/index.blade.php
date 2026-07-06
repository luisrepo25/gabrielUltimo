@extends('layouts.ferreteria')

@section('title', 'Categorías - Ferretería Guisella')

@section('content')
<div class="animate-fade-up">
    <h1 style="margin: 0;">Categorías</h1>
    <p class="subtitle">Encuentra lo que buscas por categoría de producto</p>
</div>

@if($categorias->isEmpty())
    <div style="text-align: center; padding: 60px 20px; color: var(--muted);">
        <p>No hay categorías disponibles en este momento.</p>
    </div>
@else
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 16px; margin-top: 30px;">
        @foreach($categorias as $cat)
            <a href="{{ route('categorias.productos', $cat->idcategoria) }}"
               style="display: flex; flex-direction: column; align-items: center; background: white; border-radius: 16px; border: 1px solid var(--border); padding: 24px 16px; text-decoration: none; transition: box-shadow 0.2s, transform 0.2s; text-align: center;"
               onmouseover="this.style.boxShadow='0 8px 24px rgba(0,0,0,0.12)';this.style.transform='translateY(-4px)'"
               onmouseout="this.style.boxShadow='none';this.style.transform='none'">
                @if($cat->imagen)
                    <img src="{{ asset('storage/' . $cat->imagen) }}"
                         alt="{{ $cat->nombre }}"
                         style="width: 100%; height: clamp(90px, 22vw, 140px); object-fit: contain; margin-bottom: 14px; border-radius: 8px;">
                @else
                    <div style="width: 100%; height: clamp(90px, 22vw, 140px); background: var(--bg-light); border-radius: 8px; margin-bottom: 14px; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--border)" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    </div>
                @endif
                <span style="font-weight: 700; color: var(--text-main); font-size: 0.95rem;">{{ $cat->nombre }}</span>
                @if($cat->total_productos > 0)
                    <span style="color: var(--primary); font-weight: 700; font-size: 0.85rem; margin-top: 4px;">({{ $cat->total_productos }})</span>
                @endif
            </a>
        @endforeach
    </div>
@endif
@endsection
