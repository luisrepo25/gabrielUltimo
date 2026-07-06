@extends('layouts.ferreteria')

@section('title', 'Registrar Maquinaria')

@section('content')
<div class="admin-card animate-fade-in" style="padding: 24px; margin-top: 20px; background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); max-width: 600px; margin-left: auto; margin-right: auto;">
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 1.8rem; font-weight: 700; color: #1e293b; margin: 0;">Registrar Maquinaria</h1>
        <p style="font-size: 0.9rem; color: #64748b; margin-top: 4px;">Ingrese los detalles de la nueva maquinaria de alquiler.</p>
    </div>

    <form action="{{ route('maquinarias.store') }}" method="POST">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div>
                <label for="codigo" style="display: block; font-size: 0.9rem; font-weight: 600; color: #475569; margin-bottom: 6px;">Código *</label>
                <input type="text" name="codigo" id="codigo" value="{{ old('codigo') }}" placeholder="Ej. MAQ-007" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#cbd5e1'">
            </div>
            <div>
                <label for="estado" style="display: block; font-size: 0.9rem; font-weight: 600; color: #475569; margin-bottom: 6px;">Estado *</label>
                <select name="estado" id="estado" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; outline: none; background: white; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#cbd5e1'">
                    <option value="disponible" {{ old('estado') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                    <option value="mantenimiento" {{ old('estado') == 'mantenimiento' ? 'selected' : '' }}>En Mantenimiento</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom: 16px;">
            <label for="nombre" style="display: block; font-size: 0.9rem; font-weight: 600; color: #475569; margin-bottom: 6px;">Nombre / Modelo de la Máquina *</label>
            <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" placeholder="Ej. Mezcladora de Cemento 320L" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#cbd5e1'">
        </div>

        <div style="margin-bottom: 16px;">
            <label for="descripcion" style="display: block; font-size: 0.9rem; font-weight: 600; color: #475569; margin-bottom: 6px;">Descripción / Accesorios</label>
            <textarea name="descripcion" id="descripcion" rows="3" placeholder="Detalles de la máquina, marca, motor, accesorios incluidos..." style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; outline: none; resize: vertical; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#cbd5e1'">{{ old('descripcion') }}</textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 24px;">
            <div>
                <label for="precio_hora" style="display: block; font-size: 0.9rem; font-weight: 600; color: #475569; margin-bottom: 6px;">Precio por Hora (BOB) *</label>
                <input type="number" step="0.01" name="precio_hora" id="precio_hora" value="{{ old('precio_hora') }}" min="0" placeholder="0.00" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#cbd5e1'">
            </div>
            <div>
                <label for="precio_dia" style="display: block; font-size: 0.9rem; font-weight: 600; color: #475569; margin-bottom: 6px;">Precio por Día (BOB) *</label>
                <input type="number" step="0.01" name="precio_dia" id="precio_dia" value="{{ old('precio_dia') }}" min="0" placeholder="0.00" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#cbd5e1'">
            </div>
            <div>
                <label for="garantia_sugerida" style="display: block; font-size: 0.9rem; font-weight: 600; color: #475569; margin-bottom: 6px;">Garantía Sugerida (BOB) *</label>
                <input type="number" step="0.01" name="garantia_sugerida" id="garantia_sugerida" value="{{ old('garantia_sugerida', 0) }}" min="0" placeholder="0.00" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#cbd5e1'">
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <a href="{{ route('maquinarias.index') }}" class="btn-secondary" style="padding: 10px 20px; border-radius: 8px; text-decoration: none; border: 1px solid #cbd5e1; color: #475569; font-weight: 600; background: white; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='white'">Cancelar</a>
            <button type="submit" class="btn-primary" style="padding: 10px 20px; border-radius: 8px; border: none; color: white; background: var(--primary); font-weight: 600; cursor: pointer; transition: background 0.2s;">Guardar Maquinaria</button>
        </div>
    </form>
</div>
@endsection
