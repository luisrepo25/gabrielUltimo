<section class="category-section animate-fade-up">
    <div class="category-header">
        <div class="header-left">
            <h2 class="category-title">{{ $categoria->nombre }}</h2>
            
            {{-- Subcategorías en formato Pill/Badge --}}
            @if($categoria->subcategorias->count() > 0)
                <div class="subcategory-badges">
                    @foreach($categoria->subcategorias as $sub)
                        <span class="badge-pill">{{ $sub->nombre }}</span>
                    @endforeach
                </div>
            @endif
        </div>
        
        @if($categoria->productos->count() > 0)
            <button class="scroll-hint" 
                    onclick="document.getElementById('carousel-{{ $categoria->idcategoria }}').scrollBy({left: 300, behavior: 'smooth'})"
                    style="background: none; border: none; cursor: pointer; color: var(--accent); font-size: 0.8rem; display: flex; align-items: center; gap: 8px; font-weight: 700; transition: transform 0.2s ease;">
                <span>Ver más</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </button>
        @endif
    </div>

    <div class="product-carousel" id="carousel-{{ $categoria->idcategoria }}">
        @foreach($categoria->productos as $prod)
            <div class="product-card">
                <a href="{{ route('producto.show', $prod->idproducto) }}" style="text-decoration: none; display: block; color: inherit;">
                    {{-- Imagen del Producto (URL o Placeholder) --}}
                    @if($prod->imagen)
                        <div class="product-image-container" style="height: 150px; overflow: hidden; display: flex; align-items: center; justify-content: center; background: var(--bg-light); border-radius: var(--radius-md) var(--radius-md) 0 0;">
                            <img src="{{ $prod->imagen }}" alt="{{ $prod->nombre }}" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    @else
                        <div class="product-image-placeholder">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                        </div>
                    @endif
                </a>

                @can('admin')
                    <div class="product-actions">
                        <a href="{{ route('productos.edit', $prod->idproducto) }}" class="btn-circle" title="Editar Producto">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                        </a>
                    </div>
                @endcan

                <div class="product-info">
                    <h3 class="product-name">
                        <a href="{{ route('producto.show', $prod->idproducto) }}" style="text-decoration: none; color: inherit; transition: color 0.2s ease;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='inherit'">
                            {{ $prod->nombre }}
                        </a>
                    </h3>
                    <p class="product-desc">{{ $prod->descripcion ?? 'Sin descripción.' }}</p>
                </div>

                <div class="product-footer">
                    <div class="product-price">
                        <span class="price-label">Precio</span>
                        <span class="price-value">{{ number_format($prod->precio, 2) }} Bs.</span>
                    </div>
                    <div class="product-stock">
                        {{ $prod->cantidad }} unid.
                    </div>
                </div>

                @if($prod->cantidad > 0)
                    <form action="{{ route('carrito.add') }}" method="POST" style="margin-top: 15px;" class="ajax-cart-form">
                        @csrf
                        <input type="hidden" name="idproducto" value="{{ $prod->idproducto }}">
                        <input type="hidden" name="cantidad" value="1">
                        <button type="submit" class="btn-action" style="width: 100%; padding: 8px; font-size: 0.9rem;">
                            Añadir al carrito
                        </button>
                    </form>
                @else
                    <div style="margin-top: 15px; text-align: center; color: var(--danger); font-weight: 600; font-size: 0.9rem; padding: 8px;">
                        Agotado
                    </div>
                @endif
            </div>
        @endforeach

        {{-- Solo mostramos "vío" si no hay productos NI subcategorías --}}
        @if($categoria->productos->count() == 0 && $categoria->subcategorias->count() == 0)
            <div style="padding: 40px; color: var(--muted); font-style: italic; width: 100%; text-align: center;">
                No hay artículos disponibles en esta sección.
            </div>
        @endif
    </div>

    {{-- Subcategorías (Secciones Hijas) --}}
    @if($categoria->subcategorias->count() > 0)
        <div class="sub-sections" style="padding: 0 40px 20px;">
            @foreach($categoria->subcategorias as $sub)
                 @include('inventario.categoria-recursiva', ['categoria' => $sub])
            @endforeach
        </div>
    @endif
</section>
