@extends('layouts.ferreteria')

@section('title', $marca->nombre . ' - Ferretería Guisella')

@section('content')
<style>
.shop-layout {
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 30px;
    align-items: start;
}
.shop-aside {
    background: white;
    border-radius: 16px;
    border: 1px solid var(--border);
    padding: 20px;
    position: sticky;
    top: 90px;
}
.shop-product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
}
@media (max-width: 768px) {
    .shop-layout {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    .shop-aside {
        position: static !important;
        padding: 14px 16px !important;
        top: auto !important;
    }
    .shop-aside ul {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 8px;
    }
    .shop-aside ul li {
        margin-bottom: 0 !important;
    }
    .shop-aside ul li a {
        background: var(--bg-light) !important;
        border: 1px solid var(--border) !important;
        border-radius: 20px !important;
        padding: 5px 12px !important;
        font-size: 0.82rem !important;
        white-space: nowrap;
        display: inline-flex !important;
    }
    .shop-product-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)) !important;
        gap: 14px !important;
    }
}
</style>
<div style="margin-bottom: 12px; font-size: 0.9rem; color: var(--muted);">
    <a href="{{ url('/') }}" style="color: var(--muted); text-decoration: none;">Inicio</a>
    <span style="margin: 0 6px;">›</span>
    <a href="{{ route('marcas.index') }}" style="color: var(--muted); text-decoration: none;">Marcas</a>
    <span style="margin: 0 6px;">›</span>
    <span style="color: var(--text-main);">{{ $marca->nombre }}</span>
</div>

<div class="shop-layout">

    {{-- ── PANEL IZQUIERDO ── --}}
    <aside class="shop-aside">
        <h3 style="margin: 0 0 16px 0; font-size: 1rem; color: var(--text-main);">Categorías</h3>
        <ul style="list-style: none; padding: 0; margin: 0;">
            <li style="margin-bottom: 8px;">
                <a href="{{ route('marcas.productos', $marca->id) }}"
                   style="color: {{ !request('categoria') ? 'var(--primary)' : 'var(--text-main)' }}; text-decoration: none; font-weight: {{ !request('categoria') ? '700' : '400' }}; font-size: 0.95rem;">
                    Todas las categorías
                </a>
            </li>
            @foreach($categorias as $cat)
                <li style="margin-bottom: 4px;">
                    <a href="{{ route('marcas.productos', [$marca->id, 'categoria' => $cat->idcategoria]) }}"
                       style="color: {{ request('categoria') == $cat->idcategoria ? 'var(--primary)' : 'var(--text-main)' }}; text-decoration: none; font-weight: {{ request('categoria') == $cat->idcategoria ? '700' : '400' }}; font-size: 0.9rem; display: flex; justify-content: space-between;">
                        <span>{{ $cat->nombre }}</span>
                    </a>
                    @foreach($cat->subcategorias as $sub)
                        <a href="{{ route('marcas.productos', [$marca->id, 'categoria' => $sub->idcategoria]) }}"
                           style="display: flex; justify-content: space-between; color: {{ request('categoria') == $sub->idcategoria ? 'var(--primary)' : 'var(--muted)' }}; text-decoration: none; font-size: 0.85rem; padding-left: 14px; margin-top: 3px;">
                            {{ $sub->nombre }}
                        </a>
                    @endforeach
                </li>
            @endforeach
        </ul>
    </aside>

    {{-- ── PANEL DERECHO ── --}}
    <div>
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
            <h1 style="margin: 0; font-size: 1.8rem;">{{ $marca->nombre }}</h1>
            <span style="color: var(--muted); font-size: 0.9rem;">
                Mostrando {{ $productos->firstItem() }}–{{ $productos->lastItem() }} de {{ $productos->total() }} resultados
            </span>
        </div>

        {{-- Controles --}}
        <form method="GET" action="{{ route('marcas.productos', $marca->id) }}"
              style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center; margin-bottom: 24px;">
            @if(request('categoria'))
                <input type="hidden" name="categoria" value="{{ request('categoria') }}">
            @endif
            <select name="orden" onchange="this.form.submit()"
                    style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.9rem; background: white; cursor: pointer;">
                <option value="predeterminado" {{ $orden === 'predeterminado' ? 'selected' : '' }}>Orden predeterminado</option>
                <option value="precio_asc"     {{ $orden === 'precio_asc'     ? 'selected' : '' }}>Precio: menor a mayor</option>
                <option value="precio_desc"    {{ $orden === 'precio_desc'    ? 'selected' : '' }}>Precio: mayor a menor</option>
            </select>
            <select name="mostrar" onchange="this.form.submit()"
                    style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.9rem; background: white; cursor: pointer;">
                @foreach([16, 24, 32] as $n)
                    <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>Mostrar {{ $n }}</option>
                @endforeach
            </select>
        </form>

        @if($productos->isEmpty())
            <div style="padding: 60px; text-align: center; color: var(--muted); background: white; border-radius: 16px; border: 1px solid var(--border);">
                No se encontraron productos para esta selección.
            </div>
        @else
            <div class="shop-product-grid">
                @foreach($productos as $prod)
                    <div class="product-card">
                        <a href="{{ route('producto.show', $prod->idproducto) }}" style="text-decoration: none; display: block; color: inherit;">
                            <div class="product-image-placeholder" style="height: 160px; display: flex; align-items: center; justify-content: center; background: var(--bg-light); border-radius: 12px 12px 0 0; overflow: hidden;">
                                @if($prod->imagen)
                                    <img src="{{ asset('storage/' . $prod->imagen) }}"
                                         alt="{{ $prod->nombre }}"
                                         style="max-height: 150px; max-width: 100%; object-fit: contain;">
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--border)" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                                @endif
                            </div>
                        </a>
                        <div class="product-info" style="padding: 12px;">
                            <h3 class="product-name" style="font-size: 0.95rem; margin: 0 0 4px 0;">
                                <a href="{{ route('producto.show', $prod->idproducto) }}" style="text-decoration: none; color: var(--text-main);">
                                    {{ $prod->nombre }}
                                </a>
                            </h3>
                            @if($prod->modelo)
                                <p style="margin: 0 0 4px 0; font-size: 0.8rem; color: var(--muted);">{{ $prod->modelo }}</p>
                            @endif
                            @if($prod->marca)
                                <p style="margin: 0 0 8px 0; font-size: 0.8rem; color: var(--muted);">{{ $prod->marca->nombre }}</p>
                            @endif
                            <div class="product-footer" style="display: flex; align-items: center; justify-content: space-between;">
                                <span style="font-weight: 700; color: var(--text-dark);">Bs.{{ number_format($prod->precio, 2) }}</span>
                            </div>
                        </div>
                        @if($prod->cantidad > 0)
                            <div style="padding: 0 12px 12px;">
                                <form action="{{ route('carrito.add') }}" method="POST" class="ajax-cart-form">
                                    @csrf
                                    <input type="hidden" name="idproducto" value="{{ $prod->idproducto }}">
                                    <input type="hidden" name="cantidad" value="1">
                                    <button type="submit" class="btn-action" style="width: 100%; padding: 7px; font-size: 0.85rem;">
                                        Añadir al carrito
                                    </button>
                                </form>
                            </div>
                        @else
                            <div style="padding: 0 12px 12px; text-align: center; color: var(--danger); font-size: 0.85rem; font-weight: 600;">Agotado</div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Paginación --}}
            <div style="margin-top: 30px; display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
                {{-- Anterior --}}
                @if($productos->onFirstPage())
                    <span style="padding: 8px 14px; border: 1px solid var(--border); border-radius: 8px; color: var(--muted); font-size: 0.9rem;">←</span>
                @else
                    <a href="{{ $productos->previousPageUrl() }}" style="padding: 8px 14px; border: 1px solid var(--border); border-radius: 8px; text-decoration: none; color: var(--text-main); font-size: 0.9rem;">←</a>
                @endif

                @foreach($productos->getUrlRange(1, $productos->lastPage()) as $page => $url)
                    @if($page == $productos->currentPage())
                        <span style="padding: 8px 14px; background: var(--primary); color: white; border-radius: 8px; font-size: 0.9rem; font-weight: 700;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" style="padding: 8px 14px; border: 1px solid var(--border); border-radius: 8px; text-decoration: none; color: var(--text-main); font-size: 0.9rem;">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Siguiente --}}
                @if($productos->hasMorePages())
                    <a href="{{ $productos->nextPageUrl() }}" style="padding: 8px 14px; border: 1px solid var(--border); border-radius: 8px; text-decoration: none; color: var(--text-main); font-size: 0.9rem;">→</a>
                @else
                    <span style="padding: 8px 14px; border: 1px solid var(--border); border-radius: 8px; color: var(--muted); font-size: 0.9rem;">→</span>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
