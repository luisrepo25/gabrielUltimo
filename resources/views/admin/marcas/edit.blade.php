@extends('layouts.ferreteria')

@section('title', 'Editar Marca - Admin')

@section('content')
<div class="animate-fade-up" style="max-width: 600px; margin: 0 auto;">

    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.marcas.index') }}" class="btn-action"
           style="display: inline-flex; align-items: center; gap: 8px; background: var(--bg-light); color: var(--text-main); border: 1px solid var(--border);">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Volver
        </a>
    </div>

    <div class="form-container">
        <h2 style="margin: 0 0 24px 0;">Editar Marca: {{ $marca->nombre }}</h2>

        @if($errors->any())
            <div class="alert alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.marcas.update', $marca->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-grid" style="grid-template-columns: 1fr; gap: 20px;">

                <div class="field">
                    <label>ID</label>
                    <input type="text" value="{{ $marca->id }}" disabled
                           style="width: 100%; box-sizing: border-box; background: var(--bg-light); color: var(--muted);">
                </div>

                <div class="field">
                    <label>Nombre <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $marca->nombre) }}" required
                           maxlength="50" style="width: 100%; box-sizing: border-box;">
                    @error('nombre')<span class="error-text">{{ $message }}</span>@enderror
                </div>

                <div class="field">
                    <label>Logo actual</label>
                    @if($marca->logo)
                        <div style="margin-bottom: 12px; padding: 12px; background: var(--bg-light); border-radius: 10px; border: 1px solid var(--border); display: inline-block;">
                            <img src="{{ asset('storage/' . $marca->logo) }}"
                                 alt="{{ $marca->nombre }}"
                                 style="max-height: 80px; max-width: 200px; object-fit: contain;">
                        </div>
                    @else
                        <p style="color: var(--muted); font-size: 0.9rem;">Sin logo cargado.</p>
                    @endif
                    <label style="margin-top: 8px; display: block;">Nuevo logo (opcional)</label>
                    <input type="file" name="logo" accept="image/*" style="width: 100%; box-sizing: border-box;">
                    <small style="color: var(--muted);">Dejar vacío para conservar el logo actual.</small>
                    @error('logo')<span class="error-text">{{ $message }}</span>@enderror
                </div>

                <div class="field" style="display: flex; align-items: center; gap: 12px;">
                    <input type="checkbox" name="estado" value="1" id="estado"
                           {{ old('estado', $marca->estado) ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer;">
                    <label for="estado" style="cursor: pointer; margin: 0;">Marca activa (visible en catálogo público)</label>
                </div>

            </div>

            <div style="margin-top: 28px; display: flex; gap: 12px;">
                <button type="submit" class="btn-action">Actualizar Marca</button>
                <a href="{{ route('admin.marcas.index') }}"
                   style="display: inline-flex; align-items: center; padding: 10px 20px; background: var(--bg-light); color: var(--text-main); border: 1px solid var(--border); border-radius: 10px; text-decoration: none; font-weight: 600;">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
