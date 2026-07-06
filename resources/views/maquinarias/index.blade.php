@extends('layouts.ferreteria')

@section('title', 'Catálogo de Maquinaria')

@section('content')
<div class="admin-card animate-fade-in" style="padding: 24px; margin-top: 20px; background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 1.8rem; font-weight: 700; color: #1e293b; margin: 0;">Catálogo de Maquinaria</h1>
            <p style="font-size: 0.9rem; color: #64748b; margin-top: 4px;">Inventario de maquinaria disponible para alquiler.</p>
        </div>
        @can('admin')
        <div>
            <a href="{{ route('maquinarias.create') }}" class="btn-primary" style="display: flex; align-items: center; gap: 8px; text-decoration: none; padding: 10px 20px; font-weight: 600; border-radius: 8px; background: var(--primary); color: white; border: none; cursor: pointer; transition: background 0.2s;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                Registrar Maquinaria
            </a>
        </div>
        @endcan
    </div>

    @if($maquinarias->isEmpty())
        <div style="text-align: center; padding: 48px 24px; color: #64748b;">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #cbd5e1; margin-bottom: 16px;"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            <p style="font-size: 1.1rem; font-weight: 500;">No hay maquinaria registrada aún.</p>
            @can('admin')
            <p style="font-size: 0.9rem; margin-top: 4px;">Haz clic en "Registrar Maquinaria" para agregar la primera.</p>
            @endcan
        </div>
    @else
        <div style="overflow-x: auto; margin-top: 10px;">
            <table class="inventario-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid #f1f5f9; color: #475569; font-weight: 600; font-size: 0.9rem;">
                        <th style="padding: 14px 16px;">Código</th>
                        <th style="padding: 14px 16px;">Nombre / Modelo</th>
                        <th style="padding: 14px 16px;">Descripción</th>
                        <th style="padding: 14px 16px; text-align: right;">Tarifa Hora</th>
                        <th style="padding: 14px 16px; text-align: right;">Tarifa Día</th>
                        <th style="padding: 14px 16px; text-align: right;">Garantía</th>
                        <th style="padding: 14px 16px; text-align: center;">Estado</th>
                        @can('admin')
                        <th style="padding: 14px 16px; text-align: center;">Acciones</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @foreach($maquinarias as $maq)
                        <tr style="border-bottom: 1px solid #f1f5f9; color: #334155; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 14px 16px; font-weight: 600; color: var(--primary);">{{ $maq->codigo }}</td>
                            <td style="padding: 14px 16px;">
                                <div style="font-weight: 600; color: #1e293b;">{{ $maq->nombre }}</div>
                            </td>
                            <td style="padding: 14px 16px; font-size: 0.85rem; color: #64748b; max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $maq->descripcion }}">
                                {{ $maq->descripcion ?? 'Sin descripción' }}
                            </td>
                            <td style="padding: 14px 16px; text-align: right; font-weight: 600;">{{ number_format($maq->precio_hora, 2) }} BOB</td>
                            <td style="padding: 14px 16px; text-align: right; font-weight: 600;">{{ number_format($maq->precio_dia, 2) }} BOB</td>
                            <td style="padding: 14px 16px; text-align: right; font-weight: 600; color: #475569;">{{ number_format($maq->garantia_sugerida, 2) }} BOB</td>
                            <td style="padding: 14px 16px; text-align: center;">
                                @if($maq->estado === 'disponible')
                                    <span style="background: #ecfdf5; color: #065f46; padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; border: 1px solid #a7f3d0;">Disponible</span>
                                @elseif($maq->estado === 'alquilado')
                                    <span style="background: #eff6ff; color: #1e40af; padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; border: 1px solid #bfdbfe;">Alquilado</span>
                                @else
                                    <span style="background: #fffbeb; color: #92400e; padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; border: 1px solid #fde68a;">Mantenimiento</span>
                                @endif
                            </td>
                            @can('admin')
                            <td style="padding: 14px 16px; text-align: center;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                    <a href="{{ route('maquinarias.edit', $maq->id) }}" class="btn-action" style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #e2e8f0; color: #334155; border-radius: 6px; text-decoration: none; border: none; cursor: pointer; transition: background 0.2s;" title="Editar" onmouseover="this.style.background='#cbd5e1'" onmouseout="this.style.background='#e2e8f0'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg>
                                    </a>
                                    
                                    <form action="{{ route('maquinarias.destroy', $maq->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar esta maquinaria?');" style="display: inline-block; margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action" style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #fee2e2; color: #991b1b; border-radius: 6px; border: none; cursor: pointer; transition: background 0.2s;" title="Eliminar" onmouseover="this.style.background='#fca5a5'" onmouseout="this.style.background='#fee2e2'">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endcan
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            {{ $maquinarias->links() }}
        </div>
    @endif
</div>
@endsection
