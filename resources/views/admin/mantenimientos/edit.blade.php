@extends('layouts.ferreteria')

@section('title', 'Editar Mantenimiento #' . $mantenimiento->id . ' - Ferretería Guisella')

@section('content')
<div class="animate-fade-up" style="display: flex; flex-direction: column; gap: 24px; max-width: 700px;">

    <!-- HEADER -->
    <div>
        <a href="{{ route('admin.mantenimientos.index') }}" style="color: #00AF9A; text-decoration: none; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 4px; margin-bottom: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
            Volver al listado
        </a>
        <h1 style="margin: 0; font-size: 2rem;">Editar Mantenimiento #{{ $mantenimiento->id }}</h1>
        <p class="subtitle" style="margin: 4px 0 0 0;">Maquinaria: <strong>{{ $mantenimiento->producto->nombre ?? 'N/A' }}</strong></p>
    </div>

    @if (session('error'))
        <div class="alert alert-error" style="margin: 0;">{{ session('error') }}</div>
    @endif

    <div class="card" style="background: white; border-radius: 12px; border: 1px solid var(--border); padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <form action="{{ route('admin.mantenimientos.update', $mantenimiento->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Tipo *</label>
                    <select name="tipo" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem;">
                        <option value="Preventivo" {{ $mantenimiento->tipo === 'Preventivo' ? 'selected' : '' }}>🔧 Preventivo</option>
                        <option value="Correctivo" {{ $mantenimiento->tipo === 'Correctivo' ? 'selected' : '' }}>⚠️ Correctivo</option>
                    </select>
                </div>
                <div>
                    <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Cantidad *</label>
                    <input type="number" name="cantidad" min="1" value="{{ old('cantidad', $mantenimiento->cantidad) }}" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;">
                    @error('cantidad') <span style="color: #EF4444; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Estado *</label>
                    <select name="estado" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem;">
                        <option value="Programado" {{ $mantenimiento->estado === 'Programado' ? 'selected' : '' }}>📅 Programado</option>
                        <option value="En curso" {{ $mantenimiento->estado === 'En curso' ? 'selected' : '' }}>🔄 En curso</option>
                        <option value="Finalizado" {{ $mantenimiento->estado === 'Finalizado' ? 'selected' : '' }}>✅ Finalizado</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Descripción del Trabajo *</label>
                <textarea name="descripcion" rows="3" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; box-sizing: border-box; resize: vertical;">{{ old('descripcion', $mantenimiento->descripcion) }}</textarea>
                @error('descripcion') <span style="color: #EF4444; font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Costo (Bs.) *</label>
                    <input type="number" name="costo" step="0.01" min="0" value="{{ old('costo', $mantenimiento->costo) }}" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;">
                </div>
                <div>
                    <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Fecha Inicio *</label>
                    <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', $mantenimiento->fecha_inicio ? $mantenimiento->fecha_inicio->format('Y-m-d') : '') }}" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;">
                </div>
                <div>
                    <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Fecha Fin</label>
                    <input type="date" name="fecha_fin" value="{{ old('fecha_fin', $mantenimiento->fecha_fin ? $mantenimiento->fecha_fin->format('Y-m-d') : '') }}" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;">
                </div>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Observaciones</label>
                <textarea name="observaciones" rows="2" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; box-sizing: border-box; resize: vertical;">{{ old('observaciones', $mantenimiento->observaciones) }}</textarea>
            </div>

            <div style="text-align: right;">
                <button type="submit" style="background: #00AF9A; color: white; border: none; padding: 12px 28px; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer;">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
