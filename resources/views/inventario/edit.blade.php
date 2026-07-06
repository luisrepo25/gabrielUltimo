@extends('layouts.ferreteria')

@section('title', 'Editar Producto - Ferretería Guisella')

@section('content')
<div class="animate-fade-up" style="max-width: 800px; margin: 0 auto;">
    <div class="page-header">
        <div style="display: flex; align-items: center; gap: 15px;">
            <a href="{{ route('inventario') }}" class="btn-circle" style="text-decoration: none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            </a>
            <div>
                <h1 style="margin: 0;">Editar Producto</h1>
                <p class="subtitle" style="margin: 0;">Modificando: {{ $producto->nombre }}</p>
            </div>
        </div>
        
        <form action="{{ route('productos.destroy', $producto->idproducto) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este producto permanentemente?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-logout" style="display: flex; align-items: center; gap: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                Eliminar
            </button>
        </form>
    </div>

    <form action="{{ route('productos.update', $producto->idproducto) }}" method="POST" class="form-container">
        @csrf
        @method('PUT')
        
        <div class="form-grid">
            
            <div class="field" style="grid-column: 1 / -1;">
                <label>Nombre del Producto</label>
                <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required>
            </div>

            <div class="field" style="grid-column: 1 / -1;">
                <label>Descripción</label>
                <textarea name="descripcion" rows="3" style="resize: vertical;">{{ old('descripcion', $producto->descripcion) }}</textarea>
            </div>

            <div class="field" style="grid-column: 1 / -1;">
                <label>URL de la Imagen (opcional)</label>
                <input type="url" name="imagen" value="{{ old('imagen', $producto->imagen) }}" placeholder="https://ejemplo.com/imagen.jpg">
                @error('imagen') <p class="error-text">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label>Precio (Bs)</label>
                <input type="number" step="0.01" name="precio" value="{{ old('precio', $producto->precio) }}" required>
            </div>

            <div class="field">
                <label>Stock actual</label>
                <input type="number" name="cantidad" value="{{ old('cantidad', $producto->cantidad) }}" required>
            </div>

            <div class="field">
                <label>Marca</label>
                <select name="id_marca" required>
                    @foreach($marcas as $marca)
                        <option value="{{ $marca->id }}" {{ $producto->id_marca == $marca->id ? 'selected' : '' }}>{{ $marca->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label>Categoría</label>
                <select name="id_categoria" required>
                    @foreach($categorias_formulario as $cat)
                        <option value="{{ $cat->idcategoria }}" {{ $producto->id_categoria == $cat->idcategoria ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                    @endforeach
                </select>
            </div>

        </div>

        <div style="margin-top: 30px; display: flex; justify-content: flex-end; gap: 15px;">
            <a href="{{ route('inventario') }}" class="btn-action" style="text-decoration: none;">Cancelar</a>
            <button type="submit" class="btn-save">Guardar Cambios</button>
        </div>
    </form>
</div>
@endsection
