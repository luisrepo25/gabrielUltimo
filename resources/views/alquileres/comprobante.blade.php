<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Alquiler #{{ str_pad($alquiler->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
            padding: 10px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            border-radius: 8px;
            padding: 20px;
            background: #fff;
            border: 1px solid #e2e8f0;
        }
        .header-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #ff6b00; /* Color primario ferretería */
            margin: 0 0 5px 0;
        }
        .invoice-details {
            text-align: right;
        }
        .invoice-details h2 {
            margin: 0 0 5px 0;
            font-size: 16px;
            color: #1a202c;
        }
        .info-table {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }
        .info-table th {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            color: #475569;
            font-size: 11px;
            text-transform: uppercase;
        }
        .info-table td {
            border: 1px solid #e2e8f0;
            padding: 10px 8px;
            vertical-align: top;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .details-table th {
            background-color: #ff6b00;
            color: white;
            padding: 10px;
            font-weight: 600;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }
        .details-table td {
            padding: 10px;
            border-bottom: 1px solid #f1f5f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-table {
            width: 50%;
            margin-left: 50%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .totals-table td {
            padding: 8px;
            font-size: 12px;
        }
        .total-row {
            font-size: 14px;
            font-weight: bold;
            color: #ff6b00;
            border-top: 2px solid #ff6b00;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #64748b;
            font-size: 10px;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <!-- Encabezado de Factura -->
    <table class="header-table">
        <tr>
            <td>
                <div class="title">FERRETERÍA GUISELLA</div>
                <div style="color: #64748b; font-size: 11px;">
                    Alquiler de maquinaria y herramientas profesionales.<br>
                    Dirección: Av. Principal Nro. 123, Santa Cruz, Bolivia<br>
                    Teléfono: (+591) 3-3456789 | Email: info@ferreteriaguisella.com
                </div>
            </td>
            <td class="invoice-details">
                <h2>COMPROBANTE DE ALQUILER</h2>
                <div style="font-size: 13px; font-weight: bold; color: #ff6b00; margin-bottom: 4px;">
                    NRO: #{{ str_pad($alquiler->id, 5, '0', STR_PAD_LEFT) }}
                </div>
                <div style="color: #475569;">
                    <strong>Estado Alquiler:</strong> {{ strtoupper($alquiler->estado) }}<br>
                    <strong>Método Pago:</strong> {{ $alquiler->metodoPago ? $alquiler->metodoPago->nombre : 'No especificado' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Ficha Informativa: Cliente y Empleado -->
    <table class="info-table">
        <tr>
            <th style="width: 50%;">Información del Cliente</th>
            <th style="width: 50%;">Atendido / Registrado Por</th>
        </tr>
        <tr>
            <td>
                <strong>Nombre:</strong> {{ $alquiler->cliente->nombre }} {{ $alquiler->cliente->apellido }}<br>
                <strong>C.I.:</strong> {{ $alquiler->cliente->ci }}<br>
                <strong>Teléfono:</strong> {{ $alquiler->cliente->telefono ?? 'No registrado' }}<br>
                <strong>Email:</strong> {{ $alquiler->cliente->email }}
            </td>
            <td>
                <strong>Nombre:</strong> {{ $alquiler->empleado->nombre }} {{ $alquiler->empleado->apellido }}<br>
                <strong>Cargo:</strong> Personal Operativo<br>
                <strong>Registro C.I.:</strong> {{ $alquiler->empleado->ci }}
            </td>
        </tr>
    </table>

    <!-- Ficha de Fechas y Garantía -->
    <table class="info-table">
        <tr>
            <th style="width: 50%;">Cronograma de Alquiler</th>
            <th style="width: 50%;">Condiciones de Garantía</th>
        </tr>
        <tr>
            <td>
                <strong>Fecha Inicio:</strong> {{ $alquiler->fecha_inicio->format('d/m/Y H:i') }}<br>
                <strong>Fin Estimado:</strong> {{ $alquiler->fecha_fin_estimada->format('d/m/Y H:i') }}<br>
                <strong>Fecha Devolución:</strong> {{ $alquiler->fecha_devolucion ? $alquiler->fecha_devolucion->format('d/m/Y H:i') : 'Pendiente de entrega' }}
            </td>
            <td>
                <strong>Garantía Física:</strong> {{ $alquiler->garantizado_con }}<br>
                <strong>Monto en Efectivo:</strong> {{ number_format($alquiler->monto_garantia, 2) }} Bs.<br>
                <strong>Observaciones:</strong> {{ $alquiler->observaciones ?? 'Ninguna' }}
            </td>
        </tr>
    </table>

    <!-- Detalles de las Maquinarias -->
    <table class="details-table">
        <thead>
            <tr>
                <th style="width: 45%;">Descripción de la Maquinaria</th>
                <th style="width: 20%; text-align: center;">Tarifa</th>
                <th style="width: 15%; text-align: right;">Precio Unit.</th>
                <th style="width: 10%; text-align: center;">Tiempo</th>
                <th style="width: 10%; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($alquiler->detalles as $detalle)
                <tr>
                    <td>
                        <strong style="color: #1a202c;">{{ $detalle->maquinaria ? $detalle->maquinaria->nombre : 'Maquinaria Eliminada' }}</strong><br>
                        <span style="font-size: 10px; color: #64748b;">Código: {{ $detalle->maquinaria ? $detalle->maquinaria->codigo : 'N/A' }}</span>
                    </td>
                    <td class="text-center">Por {{ $detalle->tipo_tarifa === 'hora' ? 'Hora' : 'Día' }}</td>
                    <td class="text-right">{{ number_format($detalle->precio_unitario, 2) }} Bs.</td>
                    <td class="text-center">{{ $detalle->tiempo_rentado }}</td>
                    <td class="text-right" style="font-weight: bold; color: #1a202c;">{{ number_format($detalle->precio_unitario * $detalle->tiempo_rentado, 2) }} Bs.</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totales -->
    <table class="totals-table">
        <tr>
            <td style="color: #64748b;">Total Estimado Inicial:</td>
            <td class="text-right" style="font-weight: 500;">
                {{ number_format($alquiler->total_estimado, 2) }} Bs.
            </td>
        </tr>
        @if($alquiler->total_real !== null)
        <tr class="total-row">
            <td>Total Real Liquidado:</td>
            <td class="text-right">{{ number_format($alquiler->total_real, 2) }} Bs.</td>
        </tr>
        @else
        <tr class="total-row">
            <td>Total Estimado:</td>
            <td class="text-right">{{ number_format($alquiler->total_estimado, 2) }} Bs.</td>
        </tr>
        @endif
    </table>

    <!-- Pie del comprobante -->
    <div class="footer">
        <p><strong>Ferretería Guisella - Confianza y Solidez en sus Proyectos</strong></p>
        <p>Por favor conserve este documento. La devolución tardía de la maquinaria está sujeta a cargos adicionales. Al recibir la maquinaria de vuelta, se le reintegrará la garantía correspondiente.</p>
    </div>
</div>

</body>
</html>
