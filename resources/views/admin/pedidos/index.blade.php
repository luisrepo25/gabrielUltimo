@extends('layouts.ferreteria')

@section('title', 'Pedidos de Reabastecimiento - Ferretería Guisella')

@section('content')
<div class="animate-fade-up" style="display: flex; flex-direction: column; gap: 24px;">

    <!-- HEADER -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="margin: 0; font-size: 2rem;">Pedidos de Reabastecimiento</h1>
            <p class="subtitle" style="margin: 4px 0 0 0;">Solicitudes internas para reabastecer productos con stock bajo.</p>
        </div>
        <a href="{{ route('admin.pedidos.create') }}" class="btn-productos" style="display: flex; align-items: center; gap: 8px; font-weight: 700; text-decoration: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
            Nuevo Pedido
        </a>
    </div>

    <!-- MENSAJES -->
    @if (session('success'))
        <div class="alert alert-success" style="margin: 0;">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error" style="margin: 0;">{{ session('error') }}</div>
    @endif

    <!-- TABLA -->
    <div class="card" style="background: white; border-radius: 12px; border: 1px solid var(--border); padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); overflow-x: auto;">
        @if($pedidos->count() > 0)
            <table class="fg-table" style="width: 100%; border-collapse: collapse; min-width: 700px;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border); text-align: left; background: #F9FAFB;">
                        <th style="padding: 12px 16px; font-weight: 800; color: #374151;">Nro.</th>
                        <th style="padding: 12px 16px; font-weight: 800; color: #374151;">Solicitado por</th>
                        <th style="padding: 12px 16px; font-weight: 800; color: #374151;">Fecha</th>
                        <th style="padding: 12px 16px; font-weight: 800; color: #374151; text-align: center;">Productos</th>
                        <th style="padding: 12px 16px; font-weight: 800; color: #374151;">Estado</th>
                        <th style="padding: 12px 16px; font-weight: 800; color: #374151; text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pedidos as $ped)
                        <tr style="border-bottom: 1px solid var(--border); transition: background 0.2s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='white'">
                            <td style="padding: 14px 16px; font-weight: 800; color: #1F2937;">{{ $ped->id }}</td>
                            <td style="padding: 14px 16px;">
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-weight: 700; color: #374151;">{{ $ped->empleado->nombre ?? 'N/A' }}</span>
                                    <span style="font-size: 0.75rem; color: var(--text-light);">CI: {{ $ped->ci_empleado }}</span>
                                </div>
                            </td>
                            <td style="padding: 14px 16px; color: var(--text-light); font-size: 0.9rem;">
                                {{ \Carbon\Carbon::parse($ped->fecha)->format('d/m/Y') }}
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <span style="background: rgba(0, 175, 154, 0.1); color: #00AF9A; font-weight: 800; font-size: 0.8rem; padding: 2px 8px; border-radius: 12px;">
                                    {{ $ped->detalles->count() }} ítems
                                </span>
                            </td>
                            <td style="padding: 14px 16px;">
                                @if($ped->estado === 'Pendiente')
                                    <span style="background: rgba(245, 158, 11, 0.1); color: #F59E0B; font-weight: 700; font-size: 0.8rem; padding: 4px 10px; border-radius: 6px;">⏳ Pendiente</span>
                                @elseif($ped->estado === 'Atendido')
                                    <span style="background: rgba(16, 185, 129, 0.1); color: #10B981; font-weight: 700; font-size: 0.8rem; padding: 4px 10px; border-radius: 6px;">✅ Atendido</span>
                                @else
                                    <span style="background: rgba(239, 68, 68, 0.1); color: #EF4444; font-weight: 700; font-size: 0.8rem; padding: 4px 10px; border-radius: 6px;">❌ Cancelado</span>
                                @endif
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <a href="{{ route('admin.pedidos.show', $ped->id) }}" style="color: #00AF9A; font-weight: 700; text-decoration: none; font-size: 0.85rem;">Ver Detalle</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top: 20px;">{{ $pedidos->links() }}</div>
        @else
            <div style="text-align: center; padding: 50px 20px; color: var(--text-light); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 16px;">
                <div style="background: rgba(0, 175, 154, 0.08); width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #00AF9A;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="21 8 21 21 3 21 3 8"></polyline><rect x="1" y="3" width="22" height="5"></rect></svg>
                </div>
                <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800; color: #374151;">Sin pedidos de reabastecimiento</h3>
                <p style="margin: 0; font-size: 0.9rem; max-width: 320px; line-height: 1.5;">No hay solicitudes de reabastecimiento registradas. Crea la primera desde el botón superior.</p>
            </div>
        @endif
    </div>
</div>
@endsection
