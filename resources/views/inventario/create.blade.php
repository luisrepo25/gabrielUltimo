@extends('layouts.ferreteria')

@section('title', 'Nuevo Producto - Ferretería Guisella')

@section('content')
<div class="animate-fade-up" style="max-width: 800px; margin: 0 auto;">
    <div class="page-header" style="justify-content: flex-start; gap: 15px;">
        <a href="{{ route('inventario') }}" class="btn-circle" style="text-decoration: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        </a>
        <div>
            <h1 style="margin: 0;">Registrar Producto</h1>
            <p class="subtitle" style="margin: 0;">Añade un nuevo artículo al catálogo general</p>
        </div>
    </div>

    <form action="{{ route('productos.store') }}" method="POST" class="form-container">
        @csrf
        <div class="form-grid">
            
            <div class="field">
                <label>ID del Producto</label>
                <input type="number" name="idproducto" value="{{ old('idproducto') }}" placeholder="Ej: 1001" required>
                @error('idproducto') <p class="error-text">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label>Nombre Comercial</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" placeholder="Nombre del producto" required>
            </div>

            <div class="field" style="grid-column: 1 / -1;">
                <label>Descripción Detallada</label>
                <textarea name="descripcion" rows="3" placeholder="Características técnicas, materiales, etc...">{{ old('descripcion') }}</textarea>
            </div>

            <div class="field" style="grid-column: 1 / -1;">
                <label>URL de la Imagen (opcional)</label>
                <input type="url" name="imagen" value="{{ old('imagen') }}" placeholder="https://ejemplo.com/imagen.jpg">
                @error('imagen') <p class="error-text">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label>Precio de Venta (Bs)</label>
                <input type="number" step="0.01" name="precio" value="{{ old('precio') }}" placeholder="0.00" required>
            </div>

            <div class="field">
                <label>Stock Inicial</label>
                <input type="number" name="cantidad" value="{{ old('cantidad', 0) }}" required>
            </div>

            <div class="field">
                <label>Marca</label>
                <select name="id_marca" required>
                    <option value="">Seleccionar marca...</option>
                    @foreach($marcas as $marca)
                        <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label>Categoría</label>
                <select name="id_categoria" required>
                    <option value="">Seleccionar categoría...</option>
                    @foreach($categorias_formulario as $cat)
                        <option value="{{ $cat->idcategoria }}">{{ $cat->nombre }}</option>
                    @endforeach
                </select>
            </div>

        </div>

        <div style="margin-top: 30px; display: flex; justify-content: flex-end; gap: 15px;">
            <a href="{{ route('inventario') }}" class="btn-action" style="text-decoration: none;">Cancelar</a>
            <button type="submit" class="btn-save">Guardar Producto</button>
        </div>
    </form>
</div>
@endsection
