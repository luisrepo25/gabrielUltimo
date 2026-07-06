@extends('layouts.ferreteria')

@section('title', 'Nueva Categoría - Admin')

@section('content')
<div class="animate-fade-up" style="max-width: 600px; margin: 0 auto;">

    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.categorias.index') }}" class="btn-action"
           style="display: inline-flex; align-items: center; gap: 8px; background: var(--bg-light); color: var(--text-main); border: 1px solid var(--border);">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Volver
        </a>
    </div>

    <div class="form-container">
        <h2 style="margin: 0 0 24px 0;">Nueva Categoría</h2>

        @if($errors->any())
            <div class="alert alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.categorias.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-grid" style="grid-template-columns: 1fr; gap: 20px;">

                <div class="field">
                    <label>ID de la Categoría <span style="color: var(--danger);">*</span></label>
                    <input type="number" name="idcategoria" value="{{ old('idcategoria') }}" required
                           placeholder="Ej: 20" style="width: 100%; box-sizing: border-box;">
                    @error('idcategoria')<span class="error-text">{{ $message }}</span>@enderror
                </div>

                <div class="field">
                    <label>Nombre <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required
                           maxlength="50" placeholder="Ej: Herramientas Eléctricas" style="width: 100%; box-sizing: border-box;">
                    @error('nombre')<span class="error-text">{{ $message }}</span>@enderror
                </div>

                <div class="field">
                    <label>Descripción</label>
                    <textarea name="descripcion" rows="3" maxlength="255"
                              placeholder="Descripción breve de la categoría..."
                              style="width: 100%; box-sizing: border-box; resize: vertical;">{{ old('descripcion') }}</textarea>
                    @error('descripcion')<span class="error-text">{{ $message }}</span>@enderror
                </div>

                <div class="field">
                    <label>Categoría Padre (opcional)</label>
                    <select name="id_categoria_padre" style="width: 100%; box-sizing: border-box; padding: 10px 12px; border: 1px solid var(--border); border-radius: 10px; background: white;">
                        <option value="">— Ninguna (categoría raíz) —</option>
                        @foreach($padres as $padre)
                            <option value="{{ $padre->idcategoria }}" {{ old('id_categoria_padre') == $padre->idcategoria ? 'selected' : '' }}>
                                {{ $padre->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_categoria_padre')<span class="error-text">{{ $message }}</span>@enderror
                </div>

                <div class="field">
                    <label>Imagen representativa (opcional)</label>
                    <input type="file" name="imagen" accept="image/*" style="width: 100%; box-sizing: border-box;">
                    <small style="color: var(--muted);">Formatos: JPG, PNG, WebP. Máx. 2MB.</small>
                    @error('imagen')<span class="error-text">{{ $message }}</span>@enderror
                </div>

                <div class="field" style="display: flex; align-items: center; gap: 12px;">
                    <input type="checkbox" name="estado" value="1" id="estado"
                           {{ old('estado', true) ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer;">
                    <label for="estado" style="cursor: pointer; margin: 0;">Categoría activa (visible en catálogo público)</label>
                </div>

            </div>

            <div style="margin-top: 28px; display: flex; gap: 12px;">
                <button type="submit" class="btn-action">Guardar Categoría</button>
                <a href="{{ route('admin.categorias.index') }}"
                   style="display: inline-flex; align-items: center; padding: 10px 20px; background: var(--bg-light); color: var(--text-main); border: 1px solid var(--border); border-radius: 10px; text-decoration: none; font-weight: 600;">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
