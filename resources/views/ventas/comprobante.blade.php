<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Venta #{{ str_pad($factura->nro, 6, '0', STR_PAD_LEFT) }}</title>
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
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            border-radius: 8px;
            padding: 20px;
            background: #fff;
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
            width: 40%;
            margin-left: 60%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .totals-table td {
            padding: 8px;
            font-size: 12px;
        }
        .total-row {
            font-size: 15px;
            font-weight: bold;
            color: #ff6b00;
            border-top: 2px solid #ff6b00;
        }
        .footer {
            margin-top: 50px;
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
                    Venta de materiales de construcción y herramientas de alta calidad.<br>
                    Dirección: Av. Principal Nro. 123, Santa Cruz, Bolivia<br>
                    Teléfono: (+591) 3-3456789 | Email: info@ferreteriaguisella.com
                </div>
            </td>
            <td class="invoice-details">
                <h2>COMPROBANTE DE VENTA</h2>
                <div style="font-size: 13px; font-weight: bold; color: #ff6b00; margin-bottom: 4px;">
                    NRO: #{{ str_pad($factura->nro, 6, '0', STR_PAD_LEFT) }}
                </div>
                <div style="color: #475569;">
                    <strong>Fecha Emisión:</strong> {{ \Carbon\Carbon::parse($factura->fecha)->format('d/m/Y H:i') }}<br>
                    <strong>Método Pago:</strong> {{ $factura->metodoPago ? $factura->metodoPago->nombre : 'No especificado' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Ficha Informativa: Cliente y Empleado -->
    <table class="info-table">
        <tr>
            <th style="width: 50%;">Información del Cliente</th>
            <th style="width: 50%;">Atendido Por</th>
        </tr>
        <tr>
            <td>
                @if($factura->cliente)
                    <strong>Nombre:</strong> {{ $factura->cliente->nombre }} {{ $factura->cliente->apellido }}<br>
                    <strong>C.I.:</strong> {{ $factura->cliente->ci }}<br>
                    <strong>Teléfono:</strong> {{ $factura->cliente->telefono ?? 'No registrado' }}<br>
                    <strong>Email:</strong> {{ $factura->cliente->email }}
                @else
                    <span style="color: #94a3b8; font-style: italic;">Cliente no especificado</span>
                @endif
            </td>
            <td>
                @if($factura->empleado)
                    <strong>Nombre:</strong> {{ $factura->empleado->nombre }} {{ $factura->empleado->apellido }}<br>
                    <strong>Cargo:</strong> Personal Operativo<br>
                    <strong>Registro C.I.:</strong> {{ $factura->empleado->ci }}
                @else
                    <span style="color: #94a3b8; font-style: italic;">Desconocido</span>
                @endif
            </td>
        </tr>
    </table>

    <!-- Detalles de los Productos -->
    <table class="details-table">
        <thead>
            <tr>
                <th style="width: 50%;">Descripción del Producto</th>
                <th style="width: 15%; text-align: right;">Precio Unit.</th>
                <th style="width: 10%; text-align: center;">Cant.</th>
                <th style="width: 12%; text-align: right;">Desct.</th>
                <th style="width: 13%; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($factura->detalles as $detalle)
                @php
                    $subtotal = ($detalle->precio_unitario * $detalle->cantidad) - $detalle->descuento;
                @endphp
                <tr>
                    <td>
                        <strong style="color: #1a202c;">{{ $detalle->producto ? $detalle->producto->nombre : 'Producto Eliminado' }}</strong>
                    </td>
                    <td class="text-right">{{ number_format($detalle->precio_unitario, 2) }} Bs.</td>
                    <td class="text-center">{{ $detalle->cantidad }}</td>
                    <td class="text-right">-{{ number_format($detalle->descuento, 2) }} Bs.</td>
                    <td class="text-right" style="font-weight: bold; color: #1a202c;">{{ number_format($subtotal, 2) }} Bs.</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totales -->
    <table class="totals-table">
        <tr>
            <td style="color: #64748b;">Subtotal:</td>
            <td class="text-right" style="font-weight: 500;">
                {{ number_format($factura->detalles->sum(function($d) { return $d->precio_unitario * $d->cantidad; }), 2) }} Bs.
            </td>
        </tr>
        <tr>
            <td style="color: #64748b;">Total Descuento:</td>
            <td class="text-right" style="color: #ef4444; font-weight: 500;">
                -{{ number_format($factura->detalles->sum('descuento'), 2) }} Bs.
            </td>
        </tr>
        <tr class="total-row">
            <td>Total Final:</td>
            <td class="text-right">{{ number_format($factura->total, 2) }} Bs.</td>
        </tr>
    </table>

    <!-- Pie de factura -->
    <div class="footer">
        <p><strong>¡Gracias por su compra en Ferretería Guisella!</strong></p>
        <p>Este comprobante es un documento de validez interna. Para reclamos o garantías, conserve este documento impreso.</p>
    </div>
</div>

</body>
</html>
