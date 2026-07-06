<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proforma - Ferretería Guisella</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            color: #1f2937;
        }

        .document-wrapper {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 50px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-top: 10px solid #00AF9A;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo-area h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 800;
            color: #00AF9A;
        }
        
        .logo-area p {
            margin: 5px 0 0 0;
            color: #6b7280;
            font-size: 0.9rem;
        }

        .quote-details {
            text-align: right;
        }

        .quote-details h2 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 800;
            color: #1f2937;
            text-transform: uppercase;
        }

        .quote-details p {
            margin: 5px 0 0 0;
            color: #4b5563;
            font-size: 0.95rem;
            font-weight: 600;
        }

        .client-info {
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 1px solid #e5e7eb;
        }

        .client-info p {
            margin: 5px 0;
            font-size: 0.95rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th {
            background: #f3f4f6;
            padding: 12px 15px;
            text-align: left;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #4b5563;
            border-bottom: 2px solid #d1d5db;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 0.95rem;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals-container {
            display: flex;
            justify-content: flex-end;
        }

        .totals {
            width: 300px;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
            font-size: 0.95rem;
            color: #4b5563;
        }

        .totals-row.grand-total {
            border-bottom: none;
            font-size: 1.4rem;
            font-weight: 800;
            color: #1f2937;
            margin-top: 10px;
            border-top: 2px solid #d1d5db;
            padding-top: 15px;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            color: #6b7280;
            font-size: 0.85rem;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }

        .print-btn {
            display: block;
            width: 200px;
            margin: 20px auto;
            background: #1f2937;
            color: white;
            text-align: center;
            padding: 12px 0;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            transition: background 0.2s;
        }

        .print-btn:hover {
            background: #374151;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #4b5563;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media print {
            body {
                background: white;
            }
            .document-wrapper {
                box-shadow: none;
                margin: 0;
                padding: 0;
                border-top: none;
            }
            .print-btn, .back-link {
                display: none !important;
            }
            @page { margin: 2cm; }
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="print-btn">🖨️ Imprimir Cotización</button>
    <a href="{{ route('carrito.index') }}" class="back-link">← Volver al Carrito</a>

    <div class="document-wrapper">
        <div class="header">
            <div class="logo-area">
                <h1>Ferretería Guisella</h1>
                <p>Materiales de construcción y más</p>
                <p>Av. Principal #123, Cochabamba</p>
                <p>Tel: +591 71234567</p>
            </div>
            <div class="quote-details">
                <h2>PROFORMA</h2>
                <p>Fecha: {{ date('d/m/Y') }}</p>
                <p style="color: #ef4444;">Válido por: 7 días</p>
            </div>
        </div>

        <div class="client-info">
            <p><strong>Cliente:</strong> A quien corresponda</p>
            <p><strong>Atendido por:</strong> {{ Auth::check() ? Auth::user()->name : 'Mostrador' }}</p>
            <p><strong>Referencia:</strong> Presupuesto de materiales solicitados en tienda.</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">Cant.</th>
                    <th style="width: 55%;">Descripción del Producto</th>
                    <th style="width: 20%;" class="text-right">Precio Unitario</th>
                    <th style="width: 20%;" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cartItems as $item)
                <tr>
                    <td class="text-center"><strong>{{ $item['cantidad'] }}</strong></td>
                    <td>{{ $item['nombre'] }}</td>
                    <td class="text-right">{{ number_format($item['precio'], 2) }} Bs.</td>
                    <td class="text-right"><strong>{{ number_format($item['subtotal'], 2) }} Bs.</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-container">
            <div class="totals">
                <div class="totals-row">
                    <span>Subtotal:</span>
                    <span>{{ number_format($total, 2) }} Bs.</span>
                </div>
                @if(isset($totalDiscount) && $totalDiscount > 0)
                <div class="totals-row" style="color: #ef4444; font-weight: 600;">
                    <span>Descuento:</span>
                    <span>-{{ number_format($totalDiscount, 2) }} Bs.</span>
                </div>
                @endif
                <div class="totals-row grand-total">
                    <span>TOTAL:</span>
                    <span>{{ number_format(isset($totalConDescuento) ? $totalConDescuento : $total, 2) }} Bs.</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <p><strong>Nota:</strong> Los precios están expresados en Bolivianos (Bs.) e incluyen impuestos de ley.</p>
            <p>Esta es una cotización informativa y no representa una reserva de inventario. Los precios pueden variar sin previo aviso una vez expirada la validez del documento.</p>
            <p style="margin-top: 20px; font-weight: 600;">¡Gracias por preferir a Ferretería Guisella!</p>
        </div>
    </div>

    <!-- Script to auto open print dialog (optional, but good UX) -->
    <script>
        window.onload = function() {
            // setTimeout(() => window.print(), 500);
        };
    </script>
</body>
</html>
