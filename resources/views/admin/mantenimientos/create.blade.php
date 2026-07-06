@extends('layouts.ferreteria')

@section('title', 'Programar Mantenimiento - Ferretería Guisella')

@section('content')
<div class="animate-fade-up" style="display: flex; flex-direction: column; gap: 24px; max-width: 700px;">

    <!-- HEADER -->
    <div>
        <a href="{{ route('admin.mantenimientos.index') }}" style="color: #00AF9A; text-decoration: none; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 4px; margin-bottom: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
            Volver al listado
        </a>
        <h1 style="margin: 0; font-size: 2rem;">Programar Mantenimiento</h1>
        <p class="subtitle" style="margin: 4px 0 0 0;">Registra un mantenimiento preventivo o correctivo para una maquinaria.</p>
    </div>

    @if (session('error'))
        <div class="alert alert-error" style="margin: 0;">{{ session('error') }}</div>
    @endif

    <div class="card" style="background: white; border-radius: 12px; border: 1px solid var(--border); padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <form action="{{ route('admin.mantenimientos.store') }}" method="POST">
            @csrf

            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">
                        Maquinaria * <span style="font-size: 0.72rem; color: #6B7280; font-weight: normal;">(Solo eléctricas)</span>
                    </label>
                    <select name="idproducto" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem;">
                        <option value="">Seleccionar...</option>
                        @foreach($maquinarias as $maq)
                            <option value="{{ $maq->idproducto }}" {{ old('idproducto') == $maq->idproducto ? 'selected' : '' }}>
                                {{ $maq->nombre }} (ID: {{ $maq->idproducto }}{{ $maq->modelo ? ', Mod: ' . $maq->modelo : '' }}) — Stock: {{ $maq->cantidad }}
                            </option>
                        @endforeach
                    </select>
                    @error('idproducto') <span style="color: #EF4444; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Cantidad *</label>
                    <input type="number" name="cantidad" min="1" value="{{ old('cantidad', 1) }}" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;">
                    @error('cantidad') <span style="color: #EF4444; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Tipo *</label>
                    <select name="tipo" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem;">
                        <option value="Preventivo" {{ old('tipo') === 'Preventivo' ? 'selected' : '' }}>🔧 Preventivo</option>
                        <option value="Correctivo" {{ old('tipo') === 'Correctivo' ? 'selected' : '' }}>⚠️ Correctivo</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Descripción del Trabajo *</label>
                <textarea name="descripcion" rows="3" required placeholder="Detalla el tipo de trabajo a realizar..." style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; box-sizing: border-box; resize: vertical;">{{ old('descripcion') }}</textarea>
                @error('descripcion') <span style="color: #EF4444; font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Costo Estimado (Bs.) *</label>
                    <input type="number" name="costo" step="0.01" min="0" value="{{ old('costo', 0) }}" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;">
                    @error('costo') <span style="color: #EF4444; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Fecha Inicio *</label>
                    <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', date('Y-m-d')) }}" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;">
                    @error('fecha_inicio') <span style="color: #EF4444; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Fecha Fin (estimada)</label>
                    <input type="date" name="fecha_fin" value="{{ old('fecha_fin') }}" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;">
                    @error('fecha_fin') <span style="color: #EF4444; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Observaciones (opcional)</label>
                <textarea name="observaciones" rows="2" placeholder="Notas adicionales..." style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; box-sizing: border-box; resize: vertical;">{{ old('observaciones') }}</textarea>
            </div>

            <div style="text-align: right;">
                <button type="submit" style="background: #00AF9A; color: white; border: none; padding: 12px 28px; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer;">
                    Programar Mantenimiento
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
