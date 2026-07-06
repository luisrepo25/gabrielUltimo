@extends('layouts.ferreteria')

@section('title', 'Historial de Alquileres')

@section('content')
<div class="admin-card animate-fade-in" style="padding: 24px; margin-top: 20px; background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 1.8rem; font-weight: 700; color: #1e293b; margin: 0;">Historial de Alquileres</h1>
            <p style="font-size: 0.9rem; color: #64748b; margin-top: 4px;">Alquileres de maquinaria registrados en el sistema.</p>
        </div>
        <div>
            <a href="{{ route('alquileres.create') }}" class="btn-primary" style="display: flex; align-items: center; gap: 8px; text-decoration: none; padding: 10px 20px; font-weight: 600; border-radius: 8px; background: var(--primary); color: white;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                Registrar Alquiler
            </a>
        </div>
    </div>

    <!-- Barra de búsqueda e información -->
    <form action="{{ route('alquileres.index') }}" method="GET" style="display: flex; gap: 12px; align-items: center; margin-bottom: 20px; flex-wrap: wrap;">
        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por CI o nombre de cliente..." style="flex: 1; min-width: 250px; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#cbd5e1'">
        
        <select name="estado" style="padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; outline: none; background: white; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#cbd5e1'">
            <option value="">Todos los estados</option>
            <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activo</option>
            <option value="completado" {{ request('estado') === 'completado' ? 'selected' : '' }}>Completado</option>
            <option value="atrasado" {{ request('estado') === 'atrasado' ? 'selected' : '' }}>Atrasado</option>
            <option value="cancelado" {{ request('estado') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
        </select>

        <button type="submit" class="btn-primary" style="padding: 10px 20px; border-radius: 8px; border: none; color: white; background: var(--primary); font-weight: 600; cursor: pointer; transition: background 0.2s;">Filtrar</button>
        
        @if(request()->filled('buscar') || request()->filled('estado'))
            <a href="{{ route('alquileres.index') }}" style="color: #64748b; font-size: 0.9rem; text-decoration: underline;">Limpiar</a>
        @endif
    </form>

    @if($alquileres->isEmpty())
        <div style="text-align: center; padding: 48px 24px; color: #64748b;">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #cbd5e1; margin-bottom: 16px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            <p style="font-size: 1.1rem; font-weight: 500;">No se encontraron alquileres.</p>
            <p style="font-size: 0.9rem; margin-top: 4px;">Haz clic en "Registrar Alquiler" para comenzar.</p>
        </div>
    @else
        <div style="overflow-x: auto; margin-top: 10px;">
            <table class="inventario-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid #f1f5f9; color: #475569; font-weight: 600; font-size: 0.9rem;">
                        <th style="padding: 14px 16px;">ID</th>
                        <th style="padding: 14px 16px;">Cliente</th>
                        <th style="padding: 14px 16px;">Fecha Inicio</th>
                        <th style="padding: 14px 16px;">Fin Estimado</th>
                        <th style="padding: 14px 16px;">Fecha Devolución</th>
                        <th style="padding: 14px 16px; text-align: right;">Total Estimado</th>
                        <th style="padding: 14px 16px; text-align: right;">Total Real</th>
                        <th style="padding: 14px 16px; text-align: center;">Estado</th>
                        <th style="padding: 14px 16px; text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alquileres as $alquiler)
                        <tr style="border-bottom: 1px solid #f1f5f9; color: #334155; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 14px 16px; font-weight: 600; color: var(--primary);">#{{ str_pad($alquiler->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td style="padding: 14px 16px;">
                                @if($alquiler->cliente)
                                    <div style="font-weight: 600; color: #1e293b;">{{ $alquiler->cliente->nombre }} {{ $alquiler->cliente->apellido }}</div>
                                    <span style="font-size: 0.8rem; color: #64748b;">CI: {{ $alquiler->cliente->ci }}</span>
                                @else
                                    <span style="color: #94a3b8; font-style: italic;">Desconocido</span>
                                @endif
                            </td>
                            <td style="padding: 14px 16px;">{{ $alquiler->fecha_inicio->format('d/m/Y H:i') }}</td>
                            <td style="padding: 14px 16px;">{{ $alquiler->fecha_fin_estimada->format('d/m/Y H:i') }}</td>
                            <td style="padding: 14px 16px;">
                                @if($alquiler->fecha_devolucion)
                                    {{ $alquiler->fecha_devolucion->format('d/m/Y H:i') }}
                                @else
                                    <span style="color: #94a3b8; font-style: italic;">Pendiente</span>
                                @endif
                            </td>
                            <td style="padding: 14px 16px; text-align: right; font-weight: 600;">{{ number_format($alquiler->total_estimado, 2) }} BOB</td>
                            <td style="padding: 14px 16px; text-align: right; font-weight: 700; color: #0f172a;">
                                @if($alquiler->total_real !== null)
                                    {{ number_format($alquiler->total_real, 2) }} BOB
                                @else
                                    <span style="color: #94a3b8; font-style: italic;">-</span>
                                @endif
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                @if($alquiler->estado === 'activo')
                                    <span style="background: #ecfdf5; color: #065f46; padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; border: 1px solid #a7f3d0;">Activo</span>
                                @elseif($alquiler->estado === 'completado')
                                    <span style="background: #eff6ff; color: #1e40af; padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; border: 1px solid #bfdbfe;">Completado</span>
                                @elseif($alquiler->estado === 'atrasado')
                                    <span style="background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; border: 1px solid #fca5a5;">Atrasado</span>
                                @else
                                    <span style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; border: 1px solid #cbd5e1;">Cancelado</span>
                                @endif
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                    <a href="{{ route('alquileres.show', $alquiler->id) }}" class="btn-action" style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #e2e8f0; color: #334155; border-radius: 6px; text-decoration: none; border: none; cursor: pointer; transition: background 0.2s;" title="Ver Detalle" onmouseover="this.style.background='#cbd5e1'" onmouseout="this.style.background='#e2e8f0'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    </a>
                                    <a href="{{ route('alquileres.comprobante', $alquiler->id) }}" class="btn-action" style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #e2e8f0; color: #334155; border-radius: 6px; text-decoration: none; border: none; cursor: pointer; transition: background 0.2s;" title="Comprobante PDF" onmouseover="this.style.background='#cbd5e1'" onmouseout="this.style.background='#e2e8f0'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            {{ $alquileres->links() }}
        </div>
    @endif
</div>
@endsection
