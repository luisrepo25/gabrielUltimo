@extends('layouts.ferreteria')

@section('title', 'Detalle de Alquiler')

@section('content')
<div class="animate-fade-in" style="margin-top: 20px; display: grid; grid-template-columns: 1.6fr 1fr; gap: 24px; align-items: start;">
    
    <!-- LADO IZQUIERDO: DETALLE DE MAQUINARIAS RENTADAS -->
    <div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2 style="font-size: 1.4rem; font-weight: 700; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                Alquiler #{{ str_pad($alquiler->id, 5, '0', STR_PAD_LEFT) }}
            </h2>
            <div>
                <a href="{{ route('alquileres.comprobante', $alquiler->id) }}" class="btn-primary" style="display: flex; align-items: center; gap: 8px; text-decoration: none; padding: 8px 16px; font-weight: 600; border-radius: 8px; background: #475569; color: white;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Imprimir Comprobante
                </a>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid #f1f5f9; color: #64748b; font-size: 0.85rem; font-weight: 600;">
                        <th style="padding: 12px 8px;">Máquina / Código</th>
                        <th style="padding: 12px 8px; text-align: center;">Tarifa Aplicada</th>
                        <th style="padding: 12px 8px; text-align: right;">Precio Unitario</th>
                        <th style="padding: 12px 8px; text-align: center;">Tiempo Rentado</th>
                        <th style="padding: 12px 8px; text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alquiler->detalles as $detalle)
                        <tr style="border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; color: #334155;">
                            <td style="padding: 12px 8px;">
                                <div style="font-weight: 600; color: #1e293b;">{{ $detalle->maquinaria ? $detalle->maquinaria->nombre : 'Maquinaria Eliminada' }}</div>
                                <span style="font-size: 0.75rem; color: #94a3b8;">Código: {{ $detalle->maquinaria ? $detalle->maquinaria->codigo : 'N/A' }}</span>
                            </td>
                            <td style="padding: 12px 8px; text-align: center;">
                                <span style="background: #f1f5f9; color: #475569; padding: 4px 8px; border-radius: 6px; font-size: 0.8rem; font-weight: 500;">
                                    Por {{ $detalle->tipo_tarifa === 'hora' ? 'Hora' : 'Día' }}
                                </span>
                            </td>
                            <td style="padding: 12px 8px; text-align: right; font-weight: 500;">
                                {{ number_format($detalle->precio_unitario, 2) }} BOB
                            </td>
                            <td style="padding: 12px 8px; text-align: center; font-weight: 600;">
                                {{ $detalle->tiempo_rentado }} {{ $detalle->tipo_tarifa === 'hora' ? 'hora(s)' : 'día(s)' }}
                            </td>
                            <td style="padding: 12px 8px; text-align: right; font-weight: 700; color: #0f172a;">
                                {{ number_format($detalle->precio_unitario * $detalle->tiempo_rentado, 2) }} BOB
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($alquiler->observaciones)
            <div style="margin-top: 24px; padding: 16px; background: #f8fafc; border-left: 4px solid #cbd5e1; border-radius: 4px;">
                <h4 style="margin: 0 0 6px 0; font-size: 0.9rem; font-weight: 700; color: #475569;">Observaciones:</h4>
                <p style="margin: 0; font-size: 0.9rem; color: #64748b; line-height: 1.5;">{{ $alquiler->observaciones }}</p>
            </div>
        @endif
    </div>

    <!-- LADO DERECHO: DETALLES DE CONTRATO Y DEVOLUCIÓN -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        
        <!-- ESTADO Y FECHAS -->
        <div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 1.1rem; font-weight: 700; color: #1e293b; margin: 0;">Resumen del Alquiler</h3>
                @if($alquiler->estado === 'activo')
                    <span style="background: #ecfdf5; color: #065f46; padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; border: 1px solid #a7f3d0;">Activo</span>
                @elseif($alquiler->estado === 'completado')
                    <span style="background: #eff6ff; color: #1e40af; padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; border: 1px solid #bfdbfe;">Completado</span>
                @elseif($alquiler->estado === 'atrasado')
                    <span style="background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; border: 1px solid #fca5a5;">Atrasado</span>
                @else
                    <span style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; border: 1px solid #cbd5e1;">Cancelado</span>
                @endif
            </div>

            <!-- Ficha de Cliente -->
            <div style="margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px;">
                <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 600; display: block; margin-bottom: 4px;">CLIENTE</span>
                <span style="font-weight: 700; color: #1e293b; display: block; font-size: 0.95rem;">{{ $alquiler->cliente->nombre }} {{ $alquiler->cliente->apellido }}</span>
                <span style="font-size: 0.85rem; color: #64748b; display: block;">CI: {{ $alquiler->cliente->ci }} | Tel: {{ $alquiler->cliente->telefono ?? 'N/A' }}</span>
            </div>

            <!-- Fechas -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                <div>
                    <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 600; display: block;">FECHA INICIO</span>
                    <span style="font-size: 0.9rem; font-weight: 600; color: #334155;">{{ $alquiler->fecha_inicio->format('d/m/Y H:i') }}</span>
                </div>
                <div>
                    <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 600; display: block;">FIN ESTIMADO</span>
                    <span style="font-size: 0.9rem; font-weight: 600; color: #334155;">{{ $alquiler->fecha_fin_estimada->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            @if($alquiler->fecha_devolucion)
            <div style="margin-bottom: 16px; background: #f8fafc; padding: 10px; border-radius: 6px;">
                <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; display: block;">DEVUELTO EL</span>
                <span style="font-size: 0.9rem; font-weight: 700; color: var(--primary);">{{ $alquiler->fecha_devolucion->format('d/m/Y H:i') }}</span>
            </div>
            @endif

            <!-- Garantía -->
            <div style="margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px;">
                <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 600; display: block; margin-bottom: 4px;">GARANTÍA DEPOSITADA</span>
                <span style="font-size: 0.9rem; font-weight: 600; color: #334155; display: block;">{{ $alquiler->garantizado_con }}</span>
                <span style="font-size: 0.85rem; color: #475569; display: block;">Monto efectivo: {{ number_format($alquiler->monto_garantia, 2) }} BOB</span>
            </div>

            <!-- Precios -->
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <div style="display: flex; justify-content: space-between; font-size: 0.95rem;">
                    <span style="color: #64748b;">Total Estimado:</span>
                    <span style="font-weight: 600; color: #475569;">{{ number_format($alquiler->total_estimado, 2) }} BOB</span>
                </div>
                
                @if($alquiler->total_real !== null)
                <div style="display: flex; justify-content: space-between; font-size: 1.15rem; font-weight: 800; border-top: 1px solid #e2e8f0; padding-top: 10px;">
                    <span style="color: #1e293b;">Total Real Cobrado:</span>
                    <span style="color: var(--primary);">{{ number_format($alquiler->total_real, 2) }} BOB</span>
                </div>
                @endif
            </div>
        </div>

        <!-- FORMULARIO DE REGISTRO DE DEVOLUCIÓN (SOLO SI EL ALQUILER ESTÁ ACTIVO) -->
        @if($alquiler->estado === 'activo')
        <div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 24px; border: 1.5px solid #a7f3d0; background: #fefefe;">
            <h3 style="font-size: 1.1rem; font-weight: 700; color: #065f46; margin: 0 0 10px 0; display: flex; align-items: center; gap: 6px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                Registrar Devolución
            </h3>
            <p style="font-size: 0.85rem; color: #065f46; margin-bottom: 16px;">Registre la devolución física de la maquinaria. El sistema calculará el total real cobrado en base al tiempo exacto transcurrido.</p>

            <form action="{{ route('alquileres.devolucion', $alquiler->id) }}" method="POST">
                @csrf
                <div style="margin-bottom: 16px;">
                    <label for="obs" style="display: block; font-size: 0.8rem; font-weight: 600; color: #475569; margin-bottom: 6px;">Estado de devolución / Notas</label>
                    <textarea name="observaciones" id="obs" placeholder="Ej: Devuelto en perfectas condiciones, limpio y con tanque lleno..." rows="3" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #cbd5e1; outline: none; font-size: 0.9rem; resize: vertical;"></textarea>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; background: #059669; color: white; border: none; padding: 12px; border-radius: 8px; font-size: 0.95rem; font-weight: 700; cursor: pointer; transition: background 0.2s; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);" onmouseover="this.style.background='#047857'" onmouseout="this.style.background='#059669'">
                    Confirmar Devolución y Liberar
                </button>
            </form>
        </div>
        @endif
        
        <!-- REGRESAR -->
        <a href="{{ route('alquileres.index') }}" style="text-align: center; color: #64748b; font-size: 0.9rem; text-decoration: none; font-weight: 600;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
            &larr; Volver al Historial
        </a>
    </div>
</div>
@endsection
