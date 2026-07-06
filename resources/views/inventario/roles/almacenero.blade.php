@extends('layouts.ferreteria')

@section('title', 'Gestión de Almacén - Ferretería Guisella')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;" class="animate-fade-up">
        <div>
            <h1 style="margin: 0;">Panel de Almacén</h1>
            <p class="subtitle">Control de stock e ingresos de mercadería</p>
        </div>
        <div class="badge" style="background: var(--bg); color: var(--accent); padding: 10px 20px;">
            Modo Operativo: Almacén
        </div>
    </div>

    {{-- Alertas de stock bajo --}}
    <div class="card" style="margin-bottom: 30px; border-left: 5px solid #ef4444; background: #fff1f2;">
        <h3 style="margin: 0; color: #991b1b;">⚠️ Alerta de Reposición</h3>
        <p style="font-size: 0.9rem; color: #b91c1c;">Hay productos con menos de 5 unidades en stock. Por favor revisa el catálogo abajo.</p>
    </div>

    <div class="catalog-container">
        @foreach($categorias as $categoria)
            @include('inventario.categoria-recursiva', ['categoria' => $categoria])
        @endforeach
    </div>
@endsection
