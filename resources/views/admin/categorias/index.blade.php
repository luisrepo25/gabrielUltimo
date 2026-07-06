@extends('layouts.ferreteria')

@section('title', 'Gestión de Categorías - Admin')

@section('content')
<div class="animate-fade-up">
    <div class="page-header" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 24px;">
        <div>
            <h1 style="margin: 0;">Gestión de Categorías</h1>
            <p class="subtitle" style="margin: 0;">Árbol jerárquico de categorías y subcategorías.</p>
        </div>
        <a href="{{ route('admin.categorias.create') }}" class="btn-action" style="display: inline-flex; align-items: center; gap: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nueva Categoría
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div style="background: white; border-radius: 16px; border: 1px solid var(--border); overflow: hidden;">
        <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.95rem; min-width: 620px;">
            <thead>
                <tr style="background: var(--bg-light); border-bottom: 1px solid var(--border);">
                    <th style="padding: 14px 20px; text-align: left; font-weight: 700;">ID</th>
                    <th style="padding: 14px 20px; text-align: left; font-weight: 700;">Imagen</th>
                    <th style="padding: 14px 20px; text-align: left; font-weight: 700;">Nombre</th>
                    <th style="padding: 14px 20px; text-align: left; font-weight: 700;">Categoría Padre</th>
                    <th style="padding: 14px 20px; text-align: center; font-weight: 700;">Productos</th>
                    <th style="padding: 14px 20px; text-align: center; font-weight: 700;">Estado</th>
                    <th style="padding: 14px 20px; text-align: center; font-weight: 700;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categorias as $cat)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 14px 20px; color: var(--muted);">{{ $cat->idcategoria }}</td>
                        <td style="padding: 14px 20px;">
                            @if($cat->imagen)
                                <img src="{{ asset('storage/' . $cat->imagen) }}"
                                     alt="{{ $cat->nombre }}"
                                     style="height: 44px; width: 44px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border);">
                            @else
                                <div style="height: 44px; width: 44px; background: var(--bg-light); border-radius: 8px; border: 1px solid var(--border); display: flex; align-items: center; justify-content: center;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--border)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                </div>
                            @endif
                        </td>
                        <td style="padding: 14px 20px; font-weight: 600; color: var(--text-main);">
                            @if($cat->id_categoria_padre)
                                <span style="color: var(--muted); font-size: 0.8rem; margin-right: 6px;">↳</span>
                            @endif
                            {{ $cat->nombre }}
                        </td>
                        <td style="padding: 14px 20px; color: var(--muted); font-size: 0.9rem;">
                            {{ $cat->padre?->nombre ?? '—' }}
                        </td>
                        <td style="padding: 14px 20px; text-align: center;">
                            <span style="background: var(--bg-light); padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                {{ $cat->productos_count }}
                            </span>
                        </td>
                        <td style="padding: 14px 20px; text-align: center;">
                            @if($cat->estado)
                                <span style="background: #d1fae5; color: #065f46; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">Activo</span>
                            @else
                                <span style="background: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">Inactivo</span>
                            @endif
                        </td>
                        <td style="padding: 14px 20px; text-align: center;">
                            <div style="display: flex; justify-content: center; gap: 8px;">
                                <a href="{{ route('admin.categorias.edit', $cat->idcategoria) }}"
                                   style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; background: var(--bg-light); color: var(--text-main); border: 1px solid var(--border); border-radius: 8px; text-decoration: none; font-size: 0.85rem; font-weight: 600;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                    Editar
                                </a>
                                <form action="{{ route('admin.categorias.destroy', $cat->idcategoria) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar la categoría {{ addslashes($cat->nombre) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; border-radius: 8px; cursor: pointer; font-size: 0.85rem; font-weight: 600;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 40px; text-align: center; color: var(--muted);">
                            No hay categorías registradas. <a href="{{ route('admin.categorias.create') }}" style="color: var(--primary);">Crear la primera</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
@endsection
