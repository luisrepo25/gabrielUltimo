@extends('layouts.ferreteria')

@section('title', 'Ferretería Guisella - Inventario')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="margin: 0;">Catálogo de Productos</h1>
            <p class="subtitle" style="margin: 0;">Gestión de inventario y stock</p>
        </div>

        @can('admin')
            <div class="action-buttons" style="margin: 0;">
                <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'modal-agregar')">
                    <svg xmlns="http://www.w3.org/2000/svg" style="margin-right:8px;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Nuevo Producto
                </x-primary-button>
            </div>
        @endcan
    </div>

    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="catalog-container">
        @foreach($categorias as $categoria)
            @include('inventario.categoria-recursiva', ['categoria' => $categoria])
        @endforeach
    </div>

    @auth
        {{-- MODAL AGREGAR --}}
        <x-modal name="modal-agregar" :show="$errors->has('form')" focusable>
            <div class="p-6">
                <h2 class="text-lg font-medium text-slate-900 mb-4">Registrar Nuevo Producto</h2>
                
                <form action="{{ route('productos.store') }}" method="POST">
                    @csrf
                    <div class="form-grid">
                        <div class="field">
                            <label class="text-xs font-bold text-slate-500 uppercase">ID Producto</label>
                            <input type="number" name="idproducto" value="{{ old('idproducto') }}" placeholder="Ej: 1001" required>
                            @error('idproducto') <p class="error-text">{{ $message }}</p> @enderror
                        </div>
                        <div class="field">
                            <label class="text-xs font-bold text-slate-500 uppercase">Nombre</label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}" placeholder="Nombre del producto" required>
                        </div>
                        <div class="field" style="grid-column: span 2;">
                            <label class="text-xs font-bold text-slate-500 uppercase">Descripción</label>
                            <input type="text" name="descripcion" value="{{ old('descripcion') }}" placeholder="Detalles técnicos...">
                        </div>
                        <div class="field">
                            <label class="text-xs font-bold text-slate-500 uppercase">Precio (Bs)</label>
                            <input type="number" step="0.01" name="precio" value="{{ old('precio') }}" placeholder="0.00" required>
                        </div>
                        <div class="field">
                            <label class="text-xs font-bold text-slate-500 uppercase">Stock</label>
                            <input type="number" name="cantidad" value="{{ old('cantidad') }}" placeholder="0" required>
                        </div>
                        <div class="field">
                            <label class="text-xs font-bold text-slate-500 uppercase">ID Marca</label>
                            <input type="number" name="id_marca" value="{{ old('id_marca') }}" placeholder="ID" required>
                        </div>
                        <div class="field">
                            <label class="text-xs font-bold text-slate-500 uppercase">Categoría</label>
                            <select name="id_categoria" required>
                                <option value="">Seleccionar...</option>
                                @foreach($categorias_formulario as $cat)
                                    <option value="{{ $cat->idcategoria }}" {{ old('id_categoria') == $cat->idcategoria ? 'selected' : '' }}>
                                        {{ $cat->id_categoria_padre ? '— ' : '' }}{{ $cat->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <x-secondary-button x-on:click="$dispatch('close')">Cancelar</x-secondary-button>
                        <x-primary-button>Guardar Producto</x-primary-button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- MODAL MODIFICAR --}}
        <x-modal name="modal-modificar" :show="$errors->has('form_modificar')" focusable>
            <div class="p-6">
                <div style="display: flex; justify-content: space-between; align-items: center;" class="mb-4">
                    <h2 class="text-lg font-medium text-slate-900">Modificar Producto</h2>
                    <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'modal-eliminar')">
                        Eliminar
                    </x-danger-button>
                </div>
                
                <form id="form-modificar" action="{{ url('productos') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="idproducto-modificar" name="idproducto">
                    
                    <div class="form-grid">
                        <div class="field" style="grid-column: span 2;">
                            <label class="text-xs font-bold text-slate-500 uppercase">Nombre</label>
                            <input type="text" id="modnombre" name="nombre" required>
                        </div>
                        <div class="field" style="grid-column: span 2;">
                            <label class="text-xs font-bold text-slate-500 uppercase">Descripción</label>
                            <input type="text" id="moddescripcion" name="descripcion">
                        </div>
                        <div class="field">
                            <label class="text-xs font-bold text-slate-500 uppercase">Precio (Bs)</label>
                            <input type="number" step="0.01" id="modprecio" name="precio" required>
                        </div>
                        <div class="field">
                            <label class="text-xs font-bold text-slate-500 uppercase">Stock</label>
                            <input type="number" id="modcantidad" name="cantidad" required>
                        </div>
                        <div class="field">
                            <label class="text-xs font-bold text-slate-500 uppercase">Marca</label>
                            <input type="number" id="modmarca" name="id_marca" required>
                        </div>
                        <div class="field">
                            <label class="text-xs font-bold text-slate-500 uppercase">Categoría</label>
                            <select id="modcategoria" name="id_categoria" required>
                                @foreach($categorias_formulario as $cat)
                                    <option value="{{ $cat->idcategoria }}">
                                        {{ $cat->id_categoria_padre ? '— ' : '' }}{{ $cat->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <x-secondary-button x-on:click="$dispatch('close')">Cerrar</x-secondary-button>
                        <x-primary-button>Actualizar Cambios</x-primary-button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- MODAL ELIMINAR --}}
        <x-modal name="modal-eliminar" focusable>
            <div class="p-6">
                <h2 class="text-lg font-medium text-red-600 mb-2">¿Confirmar eliminación?</h2>
                <p class="text-sm text-slate-500 mb-4">Esta acción no se puede deshacer. Se borrará el producto del sistema permanentemente.</p>
                
                <form id="form-eliminar" action="{{ url('productos') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" id="idproducto-eliminar" name="idproducto">
                    
                    <div class="bg-slate-50 p-4 rounded-lg mb-6">
                        <div class="text-sm font-bold" id="elnombre">Cargando...</div>
                        <div class="text-xs text-slate-500" id="elprecio"></div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <x-secondary-button x-on:click="$dispatch('close')">Cancelar</x-secondary-button>
                        <x-danger-button>Eliminar Producto</x-danger-button>
                    </div>
                </form>
            </div>
        </x-modal>
    @endauth
@endsection

@push('scripts')
<script>
    // Función para cargar datos en los modales y actualizar las URLs de los formularios
    window.cargarProductoModificar = function(id) {
        fetch(`/api/producto/${id}`)
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    const p = data.producto;
                    
                    // 1. Llenar campos del modal modificar
                    document.getElementById('idproducto-modificar').value = p.idproducto;
                    document.getElementById('modnombre').value = p.nombre;
                    document.getElementById('moddescripcion').value = p.descripcion || '';
                    document.getElementById('modprecio').value = p.precio;
                    document.getElementById('modcantidad').value = p.cantidad;
                    document.getElementById('modmarca').value = p.id_marca;
                    document.getElementById('modcategoria').value = p.id_categoria;
                    
                    // 2. Actualizar las rutas de los formularios (IMPORTANTE para Laravel)
                    // Como usas Route::resource, la ruta de update y destroy es /productos/{id}
                    document.getElementById('form-modificar').action = `/productos/${p.idproducto}`;
                    document.getElementById('form-eliminar').action = `/productos/${p.idproducto}`;
                    
                    // 3. Datos para el modal de confirmación de eliminación
                    document.getElementById('idproducto-eliminar').value = p.idproducto;
                    document.getElementById('elnombre').innerText = p.nombre;
                    document.getElementById('elprecio').innerText = p.precio + ' Bs.';
                    
                    // 4. Abrir el modal de edición
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-modificar' }));
                }
            });
    }
</script>
@endpush
