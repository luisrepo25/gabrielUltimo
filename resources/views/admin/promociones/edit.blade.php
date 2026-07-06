@extends('layouts.ferreteria')

@section('title', 'Editar Promoción - Ferretería Guisella')

@section('content')
<div class="animate-fade-up" style="display: flex; flex-direction: column; gap: 24px; max-width: 800px;">

    <!-- HEADER -->
    <div>
        <a href="{{ route('admin.promociones.index') }}" style="color: #00AF9A; text-decoration: none; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 4px; margin-bottom: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
            Volver al listado
        </a>
        <h1 style="margin: 0; font-size: 2rem;">Editar Promoción</h1>
        <p class="subtitle" style="margin: 4px 0 0 0;">Modifica los datos de la promoción «{{ $promocion->nombre }}».</p>
    </div>

    @if (session('error'))
        <div class="alert alert-error" style="margin: 0;">{{ session('error') }}</div>
    @endif

    <div class="card" style="background: white; border-radius: 12px; border: 1px solid var(--border); padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <form action="{{ route('admin.promociones.update', $promocion->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Nombre de la Promoción *</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $promocion->nombre) }}" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;">
                    @error('nombre') <span style="color: #EF4444; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Tipo *</label>
                    <select name="tipo" id="tipo-select" onchange="toggleTipo()" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem;">
                        <option value="Global" {{ old('tipo', $promocion->tipo) === 'Global' ? 'selected' : '' }}>🏷️ Descuento Global</option>
                        <option value="Combo" {{ old('tipo', $promocion->tipo) === 'Combo' ? 'selected' : '' }}>🎁 Combo (Precio Fijo)</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Descripción</label>
                <textarea name="descripcion" rows="2" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; box-sizing: border-box; resize: vertical;">{{ old('descripcion', $promocion->descripcion) }}</textarea>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">URL de la Imagen (opcional)</label>
                <input type="url" name="imagen" value="{{ old('imagen', $promocion->imagen) }}" placeholder="https://ejemplo.com/imagen.jpg" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;">
                @error('imagen') <span style="color: #EF4444; font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div id="descuento-group">
                    <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Descuento (%)</label>
                    <input type="number" name="descuento_porcentaje" step="0.01" min="0" max="100" value="{{ old('descuento_porcentaje', $promocion->descuento_porcentaje) }}" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;">
                </div>
                <div id="precio-combo-group" style="display: none;">
                    <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Precio Combo (Bs.)</label>
                    <input type="number" name="precio_combo" step="0.01" min="0" value="{{ old('precio_combo', $promocion->precio_combo) }}" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;">
                </div>
                <div>
                    <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Estado *</label>
                    <select name="estado" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem;">
                        <option value="Activo" {{ old('estado', $promocion->estado) === 'Activo' ? 'selected' : '' }}>✅ Activo</option>
                        <option value="Inactivo" {{ old('estado', $promocion->estado) === 'Inactivo' ? 'selected' : '' }}>⏸️ Inactivo</option>
                        <option value="Expirado" {{ old('estado', $promocion->estado) === 'Expirado' ? 'selected' : '' }}>❌ Expirado</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                <div>
                    <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Fecha Inicio *</label>
                    <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', $promocion->fecha_inicio->format('Y-m-d')) }}" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;">
                </div>
                <div>
                    <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Fecha Fin *</label>
                    <input type="date" name="fecha_fin" value="{{ old('fecha_fin', $promocion->fecha_fin->format('Y-m-d')) }}" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;">
                </div>
            </div>

            <!-- PRODUCTOS -->
            @php $productosSeleccionados = $promocion->productos->pluck('idproducto')->toArray(); @endphp
            <div style="margin-bottom: 24px;">
                <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Productos *</label>
                @error('productos') <span style="color: #EF4444; font-size: 0.8rem; display: block; margin-bottom: 8px;">{{ $message }}</span> @enderror

                <div style="max-height: 300px; overflow-y: auto; border: 1px solid var(--border); border-radius: 8px; padding: 8px;">
                    @foreach($productos as $prod)
                        <label style="display: flex; align-items: center; gap: 10px; padding: 8px 12px; border-radius: 6px; cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='transparent'">
                            <input type="checkbox" name="productos[]" value="{{ $prod->idproducto }}" {{ in_array($prod->idproducto, old('productos', $productosSeleccionados)) ? 'checked' : '' }} style="width: 16px; height: 16px; accent-color: #00AF9A;">
                            <div style="flex: 1;">
                                <span style="font-weight: 600; color: #374151;">{{ $prod->nombre }}</span>
                            </div>
                            <span style="font-weight: 800; color: #00AF9A; font-size: 0.9rem;">{{ number_format($prod->precio, 2) }} Bs.</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div style="text-align: right;">
                <button type="submit" style="background: #00AF9A; color: white; border: none; padding: 12px 28px; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer;">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleTipo() {
        const tipo = document.getElementById('tipo-select').value;
        document.getElementById('descuento-group').style.display = tipo === 'Global' ? 'block' : 'none';
        document.getElementById('precio-combo-group').style.display = tipo === 'Combo' ? 'block' : 'none';
    }
    toggleTipo();
</script>
@endpush
@endsection
