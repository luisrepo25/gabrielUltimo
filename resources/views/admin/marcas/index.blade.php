@extends('layouts.ferreteria')

@section('title', 'Gestión de Marcas - Admin')

@section('content')
<div class="animate-fade-up">
    <div class="page-header" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 24px;">
        <div>
            <h1 style="margin: 0;">Gestión de Marcas</h1>
            <p class="subtitle" style="margin: 0;">Administra los fabricantes y sus logos.</p>
        </div>
        <a href="{{ route('admin.marcas.create') }}" class="btn-action" style="display: inline-flex; align-items: center; gap: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nueva Marca
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
        <table style="width: 100%; border-collapse: collapse; font-size: 0.95rem; min-width: 580px;">
            <thead>
                <tr style="background: var(--bg-light); border-bottom: 1px solid var(--border);">
                    <th style="padding: 14px 20px; text-align: left; font-weight: 700; color: var(--text-main);">ID</th>
                    <th style="padding: 14px 20px; text-align: left; font-weight: 700; color: var(--text-main);">Logo</th>
                    <th style="padding: 14px 20px; text-align: left; font-weight: 700; color: var(--text-main);">Nombre</th>
                    <th style="padding: 14px 20px; text-align: center; font-weight: 700; color: var(--text-main);">Productos</th>
                    <th style="padding: 14px 20px; text-align: center; font-weight: 700; color: var(--text-main);">Estado</th>
                    <th style="padding: 14px 20px; text-align: center; font-weight: 700; color: var(--text-main);">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($marcas as $marca)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 14px 20px; color: var(--muted);">{{ $marca->id }}</td>
                        <td style="padding: 14px 20px;">
                            @if($marca->logo)
                                <img src="{{ asset('storage/' . $marca->logo) }}"
                                     alt="{{ $marca->nombre }}"
                                     style="height: 48px; max-width: 100px; object-fit: contain; border-radius: 6px; border: 1px solid var(--border); background: white; padding: 4px;">
                            @else
                                <span style="color: var(--muted); font-size: 0.85rem;">Sin logo</span>
                            @endif
                        </td>
                        <td style="padding: 14px 20px; font-weight: 600; color: var(--text-main);">{{ $marca->nombre }}</td>
                        <td style="padding: 14px 20px; text-align: center;">
                            <span style="background: var(--bg-light); padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                {{ $marca->productos_count }}
                            </span>
                        </td>
                        <td style="padding: 14px 20px; text-align: center;">
                            @if($marca->estado)
                                <span style="background: #d1fae5; color: #065f46; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">Activo</span>
                            @else
                                <span style="background: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">Inactivo</span>
                            @endif
                        </td>
                        <td style="padding: 14px 20px; text-align: center;">
                            <div style="display: flex; justify-content: center; gap: 8px;">
                                <a href="{{ route('admin.marcas.edit', $marca->id) }}"
                                   style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; background: var(--bg-light); color: var(--text-main); border: 1px solid var(--border); border-radius: 8px; text-decoration: none; font-size: 0.85rem; font-weight: 600;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                    Editar
                                </a>
                                <form action="{{ route('admin.marcas.destroy', $marca->id) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar la marca {{ addslashes($marca->nombre) }}?')">
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
                        <td colspan="6" style="padding: 40px; text-align: center; color: var(--muted);">
                            No hay marcas registradas. <a href="{{ route('admin.marcas.create') }}" style="color: var(--primary);">Crear la primera</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
@endsection
