@extends('layouts.ferreteria')

@section('title', 'Catálogo de Maquinaria - Ferretería Guisella')

@section('content')
<div style="margin-bottom: 12px; font-size: 0.9rem; color: var(--muted);">
    <a href="{{ url('/') }}" style="color: var(--muted); text-decoration: none;">Inicio</a>
    <span style="margin: 0 6px;">›</span>
    <span style="color: var(--text-main);">Catálogo de Maquinaria</span>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 18px;">
    @forelse($maquinarias as $maq)
        <div style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 14px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="font-weight: 700; color: var(--text-main); font-size: 1rem;">{{ $maq->nombre }}</div>
                <div style="font-size: 0.85rem; color: var(--muted); margin-top: 6px;">Código: {{ $maq->codigo }}</div>
                <div style="margin-top: 10px; display: flex; gap: 8px;">
                    <div style="font-weight: 700;">Hora: Bs.{{ number_format($maq->precio_hora, 2) }}</div>
                    <div style="font-weight: 700;">Día: Bs.{{ number_format($maq->precio_dia, 2) }}</div>
                </div>
                @if($maq->descripcion)
                    <p style="margin-top: 10px; color: var(--muted); font-size: 0.9rem;">{{ Str::limit($maq->descripcion, 120) }}</p>
                @endif
            </div>

            <div style="display:flex; gap:8px; margin-top:12px;">
                <a href="{{ route('alquileres.create') }}" class="btn-primary" style="flex:1; text-align:center; padding: 8px 10px; border-radius:8px; text-decoration:none; background:var(--primary); color:white; font-weight:700;">Alquilar</a>
                <a href="#" style="flex:1; text-align:center; padding: 8px 10px; border-radius:8px; text-decoration:none; border:1px solid var(--border); color:var(--text-main);">Ver detalles</a>
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; text-align:center; color:var(--muted); padding:40px; background:white; border-radius:12px; border:1px solid var(--border);">No hay maquinaria disponible para alquiler en este momento.</div>
    @endforelse
</div>

@endsection
