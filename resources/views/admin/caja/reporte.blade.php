<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Corte del Cajero</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #14b8a6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #0d9488;
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .info-table td {
            padding: 5px 0;
        }
        .info-table strong {
            color: #0f766e;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .summary-table th, .summary-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        .summary-table th {
            background-color: #f0fdfa;
            color: #0f766e;
            font-weight: bold;
        }
        .total-row {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .highlight-red {
            color: #ef4444;
        }
        .highlight-green {
            color: #10b981;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }
        .signature-line {
            width: 200px;
            border-top: 1px solid #333;
            margin: 50px auto 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>CORTE DEL CAJERO</h1>
        <p>Ferretería Guisella</p>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>Cajero:</strong> {{ $caja->user->nombre ?? 'Desconocido' }}</td>
            <td style="text-align: right;"><strong>Nº Caja:</strong> {{ str_pad($caja->id, 5, '0', STR_PAD_LEFT) }}</td>
        </tr>
        <tr>
            <td><strong>Fecha de Apertura:</strong> {{ \Carbon\Carbon::parse($caja->fecha_apertura)->format('d/m/Y H:i') }}</td>
            <td style="text-align: right;"><strong>Fecha de Cierre:</strong> {{ $caja->fecha_cierre ? \Carbon\Carbon::parse($caja->fecha_cierre)->format('d/m/Y H:i') : 'Pendiente' }}</td>
        </tr>
    </table>

    <table class="summary-table">
        <thead>
            <tr>
                <th colspan="2">Resumen de Movimientos</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Fondo Inicial</td>
                <td style="text-align: right;">Bs. {{ number_format($fondo, 2) }}</td>
            </tr>
            <tr>
                <td>Total Ventas Registradas</td>
                <td style="text-align: right;">Bs. {{ number_format($ventas, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>Saldo Esperado en Caja</td>
                <td style="text-align: right;">Bs. {{ number_format($fondo + $ventas, 2) }}</td>
            </tr>
            <tr>
                <td>Efectivo Real Informado</td>
                <td style="text-align: right;">Bs. {{ number_format($efectivo_real, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="summary-table">
        <thead>
            <tr>
                <th colspan="2">Diferencias</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Faltante</td>
                <td style="text-align: right;" class="{{ $faltante > 0 ? 'highlight-red' : '' }}">
                    Bs. {{ number_format($faltante, 2) }}
                </td>
            </tr>
            <tr>
                <td>Sobrante</td>
                <td style="text-align: right;" class="{{ $sobrante > 0 ? 'highlight-green' : '' }}">
                    Bs. {{ number_format($sobrante, 2) }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-line"></div>
        <p>Firma del Cajero</p>
        <p>Reporte generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

</body>
</html>
