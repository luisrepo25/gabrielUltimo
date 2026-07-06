@extends('layouts.ferreteria')

@section('title', 'Historial de Compras - Ferretería Guisella')

@section('content')
<div class="animate-fade-up" style="display: flex; flex-direction: column; gap: 24px;">
    
    <!-- HEADER -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="margin: 0; font-size: 2rem;">Historial de Compras</h1>
            <p class="subtitle" style="margin: 4px 0 0 0;">Visualiza y controla el reabastecimiento de productos y facturas de proveedores.</p>
        </div>
        <a href="{{ route('admin.compras.create') }}" class="btn-productos" style="display: flex; align-items: center; gap: 8px; font-weight: 700; text-decoration: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="21 8 21 21 3 21 3 8"></polyline><rect x="1" y="3" width="22" height="5"></rect><line x1="10" y1="12" x2="14" y2="12"></line></svg>
            Registrar Nueva Compra
        </a>
    </div>

    <!-- MENSAJES DE ÉXITO O ERROR -->
    @if (session('success'))
        <div class="alert alert-success" style="margin: 0;">
            {{ session('success') }}
        </div>
    @endif

    <!-- TABLA HISTÓRICA -->
    <div class="card" style="background: white; border-radius: 12px; border: 1px solid var(--border); padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); overflow-x: auto;">
        @if($compras->count() > 0)
            <table class="fg-table" style="width: 100%; border-collapse: collapse; min-width: 700px;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border); text-align: left; background: #F9FAFB;">
                        <th style="padding: 12px 16px; font-weight: 800; color: #374151;">Nota Nro.</th>
                        <th style="padding: 12px 16px; font-weight: 800; color: #374151;">Fecha</th>
                        <th style="padding: 12px 16px; font-weight: 800; color: #374151;">Proveedor</th>
                        <th style="padding: 12px 16px; font-weight: 800; color: #374151;">Método de Pago</th>
                        <th style="padding: 12px 16px; font-weight: 800; color: #374151; text-align: center;">Artículos</th>
                        <th style="padding: 12px 16px; font-weight: 800; color: #374151; text-align: right;">Total de Compra</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($compras as $compra)
                        <tr style="border-bottom: 1px solid var(--border); transition: background 0.2s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='white'">
                            <td style="padding: 14px 16px; font-weight: 800; color: #1F2937;">
                                {{ $compra->nro }}
                            </td>
                            <td style="padding: 14px 16px; color: var(--text-light); font-size: 0.9rem;">
                                {{ \Carbon\Carbon::parse($compra->fecha)->format('d/m/Y H:i') }}
                            </td>
                            <td style="padding: 14px 16px;">
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-weight: 700; color: #374151;">{{ $compra->proveedor->nombre ?? 'N/A' }}</span>
                                    <span style="font-size: 0.75rem; color: var(--text-light);">CI/NIT: {{ $compra->ci_proveedor }}</span>
                                </div>
                            </td>
                            <td style="padding: 14px 16px;">
                                <span style="font-size: 0.75rem; background: #EEF2F6; color: #4B5563; padding: 2px 8px; border-radius: 4px; font-weight: 700; border: 1px solid #DFE5EB;">
                                    {{ $compra->metodoPago->nombre ?? 'Efectivo' }}
                                </span>
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <span style="background: rgba(0, 175, 154, 0.1); color: #00AF9A; font-weight: 800; font-size: 0.8rem; padding: 2px 8px; border-radius: 12px; border: 1px solid rgba(0, 175, 154, 0.15);">
                                    {{ $compra->detalles->count() }} ítems
                                </span>
                            </td>
                            <td style="padding: 14px 16px; text-align: right; font-weight: 900; color: #00AF9A; font-size: 1.05rem;">
                                {{ number_format($compra->total, 2) }} Bs.
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- PAGINACIÓN -->
            <div style="margin-top: 20px;">
                {{ $compras->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 50px 20px; color: var(--text-light); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 16px;">
                <div style="background: rgba(0, 175, 154, 0.08); width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #00AF9A;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="21 8 21 21 3 21 3 8"></polyline><rect x="1" y="3" width="22" height="5"></rect></svg>
                </div>
                <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800; color: #374151;">Sin registros de reabastecimiento</h3>
                <p style="margin: 0; font-size: 0.9rem; max-width: 320px; line-height: 1.5; color: var(--text-light);">Aún no has registrado compras a proveedores en el sistema. Registra tu primera nota de compra en el botón superior.</p>
            </div>
        @endif
    </div>

</div>
@endsection
