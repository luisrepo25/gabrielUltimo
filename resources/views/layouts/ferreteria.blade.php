<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Ferretería Guisella')</title>
    <link href="{{ asset('css/ferreteria.css') }}" rel="stylesheet">
    @stack('head')
    <!-- Alpine.js (para los modales y botones) -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="fg-body has-topbar" x-data="{ mobileMenuOpen: false, sidebarOpen: false, catalogSidebarOpen: false }">

    @php
        $showSidebar = false;
        if (Auth::check() && Auth::user()->tipoPersona !== 'C') {
            // Mostrar el sidebar en TODAS las páginas si es administrador o personal operativo
            $showSidebar = true;
        }
    @endphp

    {{-- ═══════════════════════════════════════════════════
         TOPBAR UNIFICADA — se muestra en todas las páginas
         ═══════════════════════════════════════════════════ --}}
    @php
        $cartCount = 0;
        if (Auth::check()) {
            $cartCount = \Modules\Sales\Models\Carrito::where('ci_usuario', Auth::user()->ci)->sum('cantidad');
        } else {
            $cart = session()->get('carrito', []);
            foreach ($cart as $item) {
                $cartCount += $item['cantidad'];
            }
        }
    @endphp

    @php
        $categoriasMenu = \Modules\Inventory\Models\Categoria::whereNull('id_categoria_padre')->get();
        $marcasMenu = \Modules\Inventory\Models\Marca::orderBy('nombre')->get();
    @endphp

    <div class="topbar-wrapper">
        <!-- TOPBAR MAIN -->
        <div class="topbar-main">
            {{-- Botón Toggle para Sidebar admin (Solo si es admin) --}}
            @if($showSidebar)
                <button @click="sidebarOpen = !sidebarOpen" style="background: none; border: none; cursor: pointer; color: white; padding: 8px;" aria-label="Menú Admin">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </button>
            @endif

            {{-- Logo / Marca --}}
        <div style="display: flex; align-items: center; gap: 16px;">
            <a href="{{ url('/') }}" class="topbar-brand">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                Ferreteria Guisella
            </a>
            @can('admin')
                <span class="topbar-role-badge" style="background: rgba(0, 175, 154, 0.2); color: #00AF9A; padding: 4px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; border: 1px solid rgba(0, 175, 154, 0.3); white-space: nowrap;">Modo Admin</span>
            @endcan
        </div>

            {{-- Search Bar --}}
            <form action="{{ route('inventario') }}" method="GET" class="search-container" style="margin: 0; display: flex; align-items: center;">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <input type="text" name="buscar" class="search-input" placeholder="¿Qué estás buscando?" value="{{ request('buscar') }}">
            </form>

            {{-- Acciones (Usuario, Carrito) --}}
            <div class="topbar-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="action-icon" title="Mi Cuenta">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline; margin: 0; padding: 0;">
                        @csrf
                        <button type="submit" class="action-icon" style="background: none; border: none; cursor: pointer; padding: 8px;" title="Cerrar Sesión">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="action-icon" title="Iniciar Sesión">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </a>
                @endauth

                <a href="{{ route('carrito.index') }}" class="action-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                    @if($cartCount > 0)
                        <span class="cart-badge">{{ $cartCount }}</span>
                    @endif
                </a>
            </div>
        </div>

        <!-- TOPBAR SECONDARY -->
        <div class="topbar-secondary">
            @cannot('admin')
            {{-- El botón "Productos" solo se muestra a usuarios no administradores --}}
            <button class="btn-productos" @click="catalogSidebarOpen = !catalogSidebarOpen">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                Productos
            </button>
            @endcannot
        </div>
        </div>
    </div>

    {{-- El sidebar de catálogo solo es visible para usuarios NO administradores --}}
    @cannot('admin')
    <!-- MEGA-MENU / CATALOG SIDEBAR -->
    <div class="catalog-sidebar-overlay" x-show="catalogSidebarOpen" @click="catalogSidebarOpen = false" style="display: none;" x-transition.opacity></div>
    <div class="catalog-sidebar" :class="{ 'open': catalogSidebarOpen }">
        <div class="catalog-sidebar-header">
            <h3>{{ Auth::check() && Auth::user()->can('admin') ? 'Gestión de Productos' : 'Catálogo' }}</h3>
            <button @click="catalogSidebarOpen = false" style="background: none; border: none; cursor: pointer; color: var(--text-muted);">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <ul class="catalog-menu">
            @if(Auth::check() && Auth::user()->can('admin'))
                <!-- Opciones de Gestión para Admin -->
                <li>
                    <a href="{{ route('productos.create') }}">
                        <span class="catalog-menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                        </span>
                        Añadir Producto
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.categorias.index') }}">
                        <span class="catalog-menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                        </span>
                        Categorías
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.marcas.index') }}">
                        <span class="catalog-menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                        </span>
                        Marcas
                    </a>
                </li>
                <li>
                    <a href="{{ route('dashboard') }}">
                        <span class="catalog-menu-icon" style="color: var(--danger);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                        </span>
                        Stock Bajo / Crítico
                    </a>
                </li>
            @else
                <!-- Lista Pública de Categorías -->
                @foreach($categoriasMenu as $cat)
                <li>
                    <a href="{{ route('categorias.productos', $cat->idcategoria) }}">
                        <span class="catalog-menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                        </span>
                        {{ $cat->nombre }}
                        <span class="catalog-menu-arrow">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </span>
                    </a>
                </li>
                @endforeach

                {{-- Sección de Marcas --}}
                <li style="padding: 12px 24px 6px; font-size: 0.75rem; font-weight: 800; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.05em; list-style: none; margin-top: 4px; border-top: 1px solid var(--border);">
                    Marcas
                </li>
                @foreach($marcasMenu as $marca)
                <li>
                    <a href="{{ route('marcas.productos', $marca->id) }}">
                        <span class="catalog-menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                        </span>
                        {{ $marca->nombre }}
                        <span class="catalog-menu-arrow">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </span>
                    </a>
                </li>
                @endforeach
            @endif
        </ul>
    </div>
    @endcannot

    @if($showSidebar)
        {{-- Sidebar fijo que empieza debajo del topbar --}}
        <div x-show="sidebarOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             style="display: none;">
            <x-admin-sidebar />
        </div>
        {{-- Overlay oscuro para cerrar el sidebar al tocar/clicar fuera --}}
        <div x-show="sidebarOpen"
             @click="sidebarOpen = false"
             style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 949;"
             x-transition.opacity></div>
        {{-- El contenido NO se desplaza — el sidebar siempre es overlay --}}
        <div class="admin-main-content wrap @yield('wrap_class')" style="padding: 24px;">
    @else
        <div class="wrap @yield('wrap_class')">
    @endif

        {{-- Alerta de acceso denegado (redirigido por AdminMiddleware) --}}
        @if(session('error_acceso'))
            <div class="alert alert-error" style="margin-bottom: 20px;">
                🔒 {{ session('error_acceso') }}
            </div>
        @endif

        @yield('content')
    </div>



    @stack('scripts')
    
    <script>
        // Guardar la posición de desplazamiento antes de recargar
        window.addEventListener('beforeunload', function() {
            sessionStorage.setItem('scrollPosition', window.scrollY);
        });

        // Restaurar la posición de desplazamiento al cargar la página
        window.addEventListener('load', function() {
            if (sessionStorage.getItem('scrollPosition') !== null) {
                window.scrollTo(0, parseInt(sessionStorage.getItem('scrollPosition')));
                sessionStorage.removeItem('scrollPosition'); // Limpiar para que no afecte a otras navegaciones
            }
        });

        // Lógica para formularios AJAX del carrito
        document.addEventListener('submit', function(e) {
            if (e.target && e.target.classList.contains('ajax-cart-form')) {
                e.preventDefault();
                const form = e.target;
                const formData = new FormData(form);
                
                fetch(form.action, {
                    method: form.method,
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar badge del carrito
                        document.querySelectorAll('.cart-count-badge').forEach(badge => {
                            badge.textContent = data.cartCount;
                            badge.style.display = data.cartCount > 0 ? 'inline-block' : 'none';
                        });

                        // Mostrar un toast
                        showToast('¡Carrito actualizado!');

                        // Si es actualización del index del carrito
                        if (data.subtotal && data.total) {
                            const tr = form.closest('tr');
                            if (tr) {
                                const subtotalTd = tr.querySelector('.item-subtotal');
                                if (subtotalTd) subtotalTd.textContent = data.subtotal + ' Bs.';
                            }
                            const totalElements = document.querySelectorAll('.cart-total');
                            totalElements.forEach(el => el.textContent = data.total + ' Bs.');
                        }
                        
                        if (form.classList.contains('ajax-remove')) {
                            const tr = form.closest('tr');
                            if (tr) tr.remove();
                            const totalElements = document.querySelectorAll('.cart-total');
                            if(data.total) totalElements.forEach(el => el.textContent = data.total + ' Bs.');
                            
                            if (data.cartCount == 0) {
                                window.location.reload();
                            }
                        }
                    } else {
                        // Ocurrió un error (por ejemplo, stock insuficiente)
                        showToast(data.message || 'Ocurrió un error', 'error');
                        
                        // Si nos dicen a qué valor regresar el input, lo hacemos
                        if (data.revertTo !== undefined) {
                            const input = form.querySelector('input[name="cantidad"]');
                            if (input) input.value = data.revertTo;
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.textContent = message;
            toast.style.position = 'fixed';
            toast.style.bottom = '20px';
            toast.style.right = '20px';
            toast.style.background = type === 'error' ? 'var(--danger)' : 'var(--success)';
            toast.style.color = 'white';
            toast.style.padding = '12px 24px';
            toast.style.borderRadius = '8px';
            toast.style.zIndex = '9999';
            toast.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
            toast.style.animation = 'fadeInUp 0.3s ease-out';
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.5s';
                setTimeout(() => toast.remove(), 500);
            }, 2000);
        }
    </script>

    @if(!Auth::check() || (Auth::user() && (Auth::user()->tipoPersona === 'C' || strtolower(Auth::user()->tipoPersona) === 'cliente')))
        <!-- Botpress Chatbot -->
        <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
        <script src="https://files.bpcontent.cloud/2026/06/23/03/20260623031008-8E69KCVF.js" defer></script>
    @endif
</body>
</html>
