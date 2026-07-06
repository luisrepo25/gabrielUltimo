@extends('layouts.ferreteria')

@section('title', 'Detalle Pedido #' . $pedido->id . ' - Ferretería Guisella')

@section('content')
<div class="animate-fade-up" style="display: flex; flex-direction: column; gap: 24px; max-width: 800px;">

    <!-- HEADER -->
    <div>
        <a href="{{ route('admin.pedidos.index') }}" style="color: #00AF9A; text-decoration: none; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 4px; margin-bottom: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
            Volver al listado
        </a>
        <h1 style="margin: 0; font-size: 2rem;">Pedido de Reabastecimiento #{{ $pedido->id }}</h1>
    </div>

    <!-- MENSAJES -->
    @if (session('success'))
        <div class="alert alert-success" style="margin: 0;">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error" style="margin: 0;">{{ session('error') }}</div>
    @endif

    <!-- INFO GENERAL -->
    <div class="card" style="background: white; border-radius: 12px; border: 1px solid var(--border); padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
            <div>
                <p style="margin: 0 0 4px 0; color: var(--text-light); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Solicitado por</p>
                <p style="margin: 0; font-weight: 800; font-size: 1rem;">{{ $pedido->empleado->nombre ?? 'N/A' }}</p>
                <p style="margin: 0; color: var(--text-light); font-size: 0.8rem;">CI: {{ $pedido->ci_empleado }}</p>
            </div>
            <div>
                <p style="margin: 0 0 4px 0; color: var(--text-light); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Fecha</p>
                <p style="margin: 0; font-weight: 700;">{{ \Carbon\Carbon::parse($pedido->fecha)->format('d/m/Y') }}</p>
            </div>
            <div>
                <p style="margin: 0 0 4px 0; color: var(--text-light); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Estado</p>
                @if($pedido->estado === 'Pendiente')
                    <span style="background: rgba(245, 158, 11, 0.1); color: #F59E0B; font-weight: 700; padding: 4px 10px; border-radius: 6px;">⏳ Pendiente</span>
                @elseif($pedido->estado === 'Atendido')
                    <span style="background: rgba(16, 185, 129, 0.1); color: #10B981; font-weight: 700; padding: 4px 10px; border-radius: 6px;">✅ Atendido</span>
                @else
                    <span style="background: rgba(239, 68, 68, 0.1); color: #EF4444; font-weight: 700; padding: 4px 10px; border-radius: 6px;">❌ Cancelado</span>
                @endif
            </div>
        </div>

        @if($pedido->observaciones)
        <div style="margin-top: 16px;">
            <p style="margin: 0 0 4px 0; color: var(--text-light); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Observaciones</p>
            <p style="margin: 0; color: #374151; line-height: 1.5;">{{ $pedido->observaciones }}</p>
        </div>
        @endif
    </div>

    <!-- PRODUCTOS SOLICITADOS -->
    <div class="card" style="background: white; border-radius: 12px; border: 1px solid var(--border); padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <h3 style="margin: 0 0 16px 0; font-size: 1.1rem;">Productos Solicitados</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border); background: #F9FAFB;">
                    <th style="padding: 10px 12px; text-align: left; font-weight: 800; color: #374151;">Producto</th>
                    <th style="padding: 10px 12px; text-align: center; font-weight: 800; color: #374151;">Stock Actual</th>
                    <th style="padding: 10px 12px; text-align: center; font-weight: 800; color: #374151;">Cant. Sugerida</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pedido->detalles as $det)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 12px; font-weight: 600;">{{ $det->producto->nombre ?? 'Producto #' . $det->idproducto }}</td>
                        <td style="padding: 12px; text-align: center;">
                            @php $stock = $det->producto->cantidad ?? 0; @endphp
                            <span style="{{ $stock < 5 ? 'color: #DC2626; font-weight: 900;' : '' }}">{{ $stock }}</span>
                        </td>
                        <td style="padding: 12px; text-align: center; font-weight: 800; color: #00AF9A;">{{ $det->cantidad_sugerida }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- ACCIONES (solo si está pendiente) -->
    @if($pedido->estado === 'Pendiente')
    <div style="display: flex; gap: 12px; justify-content: flex-end; flex-wrap: wrap;">
        <form action="{{ route('admin.pedidos.cancelar', $pedido->id) }}" method="POST">
            @csrf
            <button type="submit" style="background: #FEF2F2; color: #EF4444; border: 1px solid #FECACA; padding: 10px 24px; border-radius: 8px; font-weight: 700; cursor: pointer;" onclick="return confirm('¿Cancelar este pedido?')">
                Cancelar Pedido
            </button>
        </form>
        <a href="{{ route('admin.compras.create', ['pedido_id' => $pedido->id]) }}" style="background: rgba(59, 130, 246, 0.1); color: #3B82F6; border: 1px solid rgba(59, 130, 246, 0.3); padding: 10px 24px; border-radius: 8px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
            Ir a Registrar Compra
        </a>
        <form action="{{ route('admin.pedidos.atender', $pedido->id) }}" method="POST">
            @csrf
            <button type="submit" style="background: #00AF9A; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 700; cursor: pointer;" onclick="return confirm('¿Marcar este pedido como atendido?')">
                Marcar como Atendido
            </button>
        </form>
    </div>
    @endif

</div>
@endsection
