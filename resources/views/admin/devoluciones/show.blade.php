@extends('layouts.ferreteria')

@section('title', 'Detalle Devolución #' . $devolucion->id . ' - Ferretería Guisella')

@section('content')
<div class="animate-fade-up" style="display: flex; flex-direction: column; gap: 24px; max-width: 800px;">

    <!-- HEADER -->
    <div>
        <a href="{{ route('admin.devoluciones.index') }}" style="color: #00AF9A; text-decoration: none; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 4px; margin-bottom: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
            Volver al listado
        </a>
        <h1 style="margin: 0; font-size: 2rem;">Devolución / Garantía #{{ $devolucion->id }}</h1>
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
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <p style="margin: 0 0 4px 0; color: var(--text-light); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Factura Asociada</p>
                <p style="margin: 0; font-weight: 800; font-size: 1.1rem; color: #00AF9A;">Nro. {{ $devolucion->nro_factura }}</p>
            </div>
            <div>
                <p style="margin: 0 0 4px 0; color: var(--text-light); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Tipo</p>
                @if($devolucion->tipo === 'Devolución')
                    <span style="background: rgba(239, 68, 68, 0.1); color: #EF4444; font-weight: 700; padding: 4px 10px; border-radius: 6px;">{{ $devolucion->tipo }}</span>
                @else
                    <span style="background: rgba(59, 130, 246, 0.1); color: #3B82F6; font-weight: 700; padding: 4px 10px; border-radius: 6px;">{{ $devolucion->tipo }}</span>
                @endif
            </div>
            <div>
                <p style="margin: 0 0 4px 0; color: var(--text-light); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Cliente</p>
                <p style="margin: 0; font-weight: 700;">{{ $devolucion->factura->cliente->nombre ?? 'N/A' }} {{ $devolucion->factura->cliente->apellido ?? '' }}</p>
            </div>
            <div>
                <p style="margin: 0 0 4px 0; color: var(--text-light); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Fecha</p>
                <p style="margin: 0; font-weight: 700;">{{ \Carbon\Carbon::parse($devolucion->fecha)->format('d/m/Y') }}</p>
            </div>
            <div>
                <p style="margin: 0 0 4px 0; color: var(--text-light); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Estado</p>
                @if($devolucion->estado === 'Pendiente')
                    <span style="background: rgba(245, 158, 11, 0.1); color: #F59E0B; font-weight: 700; padding: 4px 10px; border-radius: 6px;">⏳ Pendiente</span>
                @elseif($devolucion->estado === 'Aprobado')
                    <span style="background: rgba(16, 185, 129, 0.1); color: #10B981; font-weight: 700; padding: 4px 10px; border-radius: 6px;">✅ Aprobado</span>
                @else
                    <span style="background: rgba(239, 68, 68, 0.1); color: #EF4444; font-weight: 700; padding: 4px 10px; border-radius: 6px;">❌ Rechazado</span>
                @endif
            </div>
            <div>
                <p style="margin: 0 0 4px 0; color: var(--text-light); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Gestionado por</p>
                <p style="margin: 0; font-weight: 700;">{{ $devolucion->empleado->nombre ?? 'N/A' }}</p>
            </div>
        </div>

        <div style="margin-top: 16px;">
            <p style="margin: 0 0 4px 0; color: var(--text-light); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Motivo</p>
            <p style="margin: 0; color: #374151; line-height: 1.5;">{{ $devolucion->motivo }}</p>
        </div>

        @if($devolucion->observaciones)
        <div style="margin-top: 12px;">
            <p style="margin: 0 0 4px 0; color: var(--text-light); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Observaciones</p>
            <p style="margin: 0; color: #374151; line-height: 1.5;">{{ $devolucion->observaciones }}</p>
        </div>
        @endif
    </div>

    <!-- PRODUCTOS -->
    <div class="card" style="background: white; border-radius: 12px; border: 1px solid var(--border); padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <h3 style="margin: 0 0 16px 0; font-size: 1.1rem;">Productos Incluidos</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border); background: #F9FAFB;">
                    <th style="padding: 10px 12px; text-align: left; font-weight: 800; color: #374151;">Producto</th>
                    <th style="padding: 10px 12px; text-align: center; font-weight: 800; color: #374151;">Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($devolucion->detalles as $det)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 12px;">{{ $det->producto->nombre ?? 'Producto #' . $det->idproducto }}</td>
                        <td style="padding: 12px; text-align: center; font-weight: 800;">{{ $det->cantidad }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- ACCIONES (solo si está pendiente) -->
    @if($devolucion->estado === 'Pendiente')
    <div style="display: flex; gap: 12px; justify-content: flex-end;">
        <form action="{{ route('admin.devoluciones.rechazar', $devolucion->id) }}" method="POST">
            @csrf
            <button type="submit" style="background: #FEF2F2; color: #EF4444; border: 1px solid #FECACA; padding: 10px 24px; border-radius: 8px; font-weight: 700; cursor: pointer;" onclick="return confirm('¿Rechazar esta devolución?')">
                Rechazar
            </button>
        </form>
        <form action="{{ route('admin.devoluciones.aprobar', $devolucion->id) }}" method="POST">
            @csrf
            <button type="submit" style="background: #00AF9A; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 700; cursor: pointer;" onclick="return confirm('¿Aprobar esta devolución? Se reintegrará el stock al inventario.')">
                Aprobar y Reintegrar Stock
            </button>
        </form>
    </div>
    @endif

</div>
@endsection
