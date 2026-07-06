@extends('layouts.ferreteria')

@section('title', 'Registrar Devolución/Garantía - Ferretería Guisella')

@section('content')
<div class="animate-fade-up" style="display: flex; flex-direction: column; gap: 24px; max-width: 900px;">

    <!-- HEADER -->
    <div>
        <a href="{{ route('admin.devoluciones.index') }}" style="color: #00AF9A; text-decoration: none; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 4px; margin-bottom: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
            Volver al listado
        </a>
        <h1 style="margin: 0; font-size: 2rem;">Registrar Devolución / Garantía</h1>
        <p class="subtitle" style="margin: 4px 0 0 0;">Busca la nota de venta e indica los productos a devolver o cubrir por garantía.</p>
    </div>

    <!-- BUSCAR FACTURA -->
    <div class="card" style="background: white; border-radius: 12px; border: 1px solid var(--border); padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <h3 style="margin: 0 0 16px 0; font-size: 1.1rem;">1. Buscar Nota de Venta</h3>
        <div style="display: flex; gap: 12px; align-items: flex-end;">
            <div style="flex: 1;">
                <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Número de Factura</label>
                <input type="number" id="nro_factura" placeholder="Ej: 1001" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;">
            </div>
            <button onclick="buscarFactura()" style="background: #00AF9A; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; white-space: nowrap;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                Buscar
            </button>
        </div>
        <div id="factura-info" style="display: none; margin-top: 16px; padding: 16px; background: #F0FDF4; border-radius: 8px; border: 1px solid #BBF7D0;">
        </div>
        <div id="factura-error" style="display: none; margin-top: 16px; padding: 12px 16px; background: #FEF2F2; border-radius: 8px; border: 1px solid #FECACA; color: #EF4444; font-weight: 600;">
        </div>
    </div>

    <!-- FORMULARIO DE DEVOLUCIÓN -->
    <div id="devolucion-form" style="display: none;">
        <div class="card" style="background: white; border-radius: 12px; border: 1px solid var(--border); padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
            <h3 style="margin: 0 0 16px 0; font-size: 1.1rem;">2. Datos de la Devolución</h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Tipo</label>
                    <select id="tipo" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem;">
                        <option value="Devolución">Devolución</option>
                        <option value="Garantía">Garantía</option>
                    </select>
                </div>
                <div>
                    <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Motivo</label>
                    <input type="text" id="motivo" placeholder="Ej: Producto defectuoso" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;">
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Observaciones (opcional)</label>
                <textarea id="observaciones" rows="2" placeholder="Detalles adicionales..." style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; box-sizing: border-box; resize: vertical;"></textarea>
            </div>

            <h3 style="margin: 24px 0 16px 0; font-size: 1.1rem;">3. Productos a Devolver</h3>
            <div style="overflow-x: auto;">
                <table id="productos-tabla" style="width: 100%; border-collapse: collapse; min-width: 500px;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border); text-align: left; background: #F9FAFB;">
                            <th style="padding: 10px 12px; font-weight: 800; color: #374151;">Seleccionar</th>
                            <th style="padding: 10px 12px; font-weight: 800; color: #374151;">Producto</th>
                            <th style="padding: 10px 12px; font-weight: 800; color: #374151; text-align: center;">Cant. Comprada</th>
                            <th style="padding: 10px 12px; font-weight: 800; color: #374151; text-align: center;">Cant. a Devolver</th>
                        </tr>
                    </thead>
                    <tbody id="productos-body">
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 24px; text-align: right;">
                <button onclick="registrarDevolucion()" style="background: #00AF9A; color: white; border: none; padding: 12px 28px; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer;">
                    Registrar Devolución
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let facturaData = null;

    function buscarFactura() {
        const nro = document.getElementById('nro_factura').value;
        if (!nro) return;

        fetch(`/api/devoluciones/factura/${nro}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                facturaData = data.factura;
                document.getElementById('factura-error').style.display = 'none';

                const info = document.getElementById('factura-info');
                info.style.display = 'block';
                info.innerHTML = `
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="color: #166534;">Factura Nro. ${data.factura.nro}</strong><br>
                            <span style="color: #4B5563; font-size: 0.85rem;">Cliente: ${data.factura.cliente ? data.factura.cliente.nombre + ' ' + (data.factura.cliente.apellido || '') : 'N/A'}</span>
                        </div>
                        <span style="font-weight: 800; color: #166534; font-size: 1.1rem;">${parseFloat(data.factura.total).toFixed(2)} Bs.</span>
                    </div>
                `;

                // Llenar tabla de productos
                const tbody = document.getElementById('productos-body');
                tbody.innerHTML = '';
                data.factura.detalles.forEach(det => {
                    tbody.innerHTML += `
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 10px 12px; text-align: center;">
                                <input type="checkbox" class="prod-check" data-id="${det.id_producto}" data-max="${det.cantidad}">
                            </td>
                            <td style="padding: 10px 12px; font-weight: 600;">${det.producto ? det.producto.nombre : 'Producto #' + det.id_producto}</td>
                            <td style="padding: 10px 12px; text-align: center; font-weight: 700;">${det.cantidad}</td>
                            <td style="padding: 10px 12px; text-align: center;">
                                <input type="number" class="prod-qty" data-id="${det.id_producto}" min="1" max="${det.cantidad}" value="1" style="width: 70px; padding: 6px; border: 1px solid var(--border); border-radius: 6px; text-align: center;">
                            </td>
                        </tr>
                    `;
                });

                document.getElementById('devolucion-form').style.display = 'block';
            } else {
                document.getElementById('factura-info').style.display = 'none';
                document.getElementById('devolucion-form').style.display = 'none';
                const err = document.getElementById('factura-error');
                err.style.display = 'block';
                err.textContent = data.message || 'Factura no encontrada.';
            }
        })
        .catch(() => {
            document.getElementById('factura-info').style.display = 'none';
            document.getElementById('devolucion-form').style.display = 'none';
            const err = document.getElementById('factura-error');
            err.style.display = 'block';
            err.textContent = 'Factura no encontrada.';
        });
    }

    function registrarDevolucion() {
        const checks = document.querySelectorAll('.prod-check:checked');
        if (checks.length === 0) {
            alert('Selecciona al menos un producto.');
            return;
        }

        const motivo = document.getElementById('motivo').value;
        if (!motivo) {
            alert('Indica el motivo de la devolución.');
            return;
        }

        const productos = [];
        checks.forEach(chk => {
            const id = chk.dataset.id;
            const qty = document.querySelector(`.prod-qty[data-id="${id}"]`).value;
            productos.push({ id: parseInt(id), cantidad: parseInt(qty) });
        });

        const payload = {
            nro_factura: facturaData.nro,
            tipo: document.getElementById('tipo').value,
            motivo: motivo,
            observaciones: document.getElementById('observaciones').value,
            productos: productos,
        };

        fetch('{{ route("admin.devoluciones.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("admin.devoluciones.index") }}';
            } else {
                alert(data.message || 'Error al registrar.');
            }
        })
        .catch(e => alert('Error de conexión.'));
    }
</script>
@endpush
@endsection
