@extends('layouts.ferreteria')

@section('title', 'Historial de Ventas')

@section('content')
<div class="admin-card animate-fade-in" style="padding: 24px; margin-top: 20px; background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 1.8rem; font-weight: 700; color: #1e293b; margin: 0;">Historial de Ventas</h1>
            <p style="font-size: 0.9rem; color: #64748b; margin-top: 4px;">Ventas registradas en tienda física.</p>
        </div>
        <div>
            <a href="{{ route('ventas.create') }}" class="btn-primary" style="display: flex; align-items: center; gap: 8px; text-decoration: none; padding: 10px 20px; font-weight: 600; border-radius: 8px; background: var(--primary); color: white;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                Nueva Venta (POS)
            </a>
        </div>
    </div>

    @if($ventas->isEmpty())
        <div style="text-align: center; padding: 48px 24px; color: #64748b;">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #cbd5e1; margin-bottom: 16px;"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
            <p style="font-size: 1.1rem; font-weight: 500;">No se han registrado ventas aún.</p>
            <p style="font-size: 0.9rem; margin-top: 4px;">Haz clic en "Nueva Venta" para comenzar.</p>
        </div>
    @else
        <div style="overflow-x: auto; margin-top: 10px;">
            <table class="inventario-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid #f1f5f9; color: #475569; font-weight: 600; font-size: 0.9rem;">
                        <th style="padding: 14px 16px;">Nro Factura</th>
                        <th style="padding: 14px 16px;">Fecha</th>
                        <th style="padding: 14px 16px;">Cliente</th>
                        <th style="padding: 14px 16px;">Cajero</th>
                        <th style="padding: 14px 16px;">Método de Pago</th>
                        <th style="padding: 14px 16px; text-align: right;">Total</th>
                        <th style="padding: 14px 16px; text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ventas as $venta)
                        <tr style="border-bottom: 1px solid #f1f5f9; color: #334155; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 14px 16px; font-weight: 600; color: var(--primary);">#{{ str_pad($venta->nro, 6, '0', STR_PAD_LEFT) }}</td>
                            <td style="padding: 14px 16px;">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</td>
                            <td style="padding: 14px 16px;">
                                @if($venta->cliente)
                                    <div style="font-weight: 500;">{{ $venta->cliente->nombre }} {{ $venta->cliente->apellido }}</div>
                                    <span style="font-size: 0.8rem; color: #64748b;">CI: {{ $venta->cliente->ci }}</span>
                                @else
                                    <span style="color: #94a3b8; font-style: italic;">Desconocido</span>
                                @endif
                            </td>
                            <td style="padding: 14px 16px;">{{ $venta->empleado ? $venta->empleado->nombre : 'Desconocido' }}</td>
                            <td style="padding: 14px 16px;">
                                <span style="background: #f1f5f9; color: #475569; padding: 4px 8px; border-radius: 6px; font-size: 0.8rem; font-weight: 500;">
                                    {{ $venta->metodoPago ? $venta->metodoPago->nombre : 'No especificado' }}
                                </span>
                            </td>
                            <td style="padding: 14px 16px; text-align: right; font-weight: 700; color: #0f172a;">
                                {{ number_format($venta->total, 2) }} BOB
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <a href="{{ route('ventas.comprobante', $venta->nro) }}" class="btn-action" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #e2e8f0; color: #334155; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 500; border: none; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#cbd5e1'" onmouseout="this.style.background='#e2e8f0'">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                    PDF
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            {{ $ventas->links() }}
        </div>
    @endif
</div>
@endsection
