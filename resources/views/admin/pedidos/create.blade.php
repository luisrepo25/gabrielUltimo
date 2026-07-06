@extends('layouts.ferreteria')

@section('title', 'Nuevo Pedido de Reabastecimiento - Ferretería Guisella')

@section('content')
<div class="animate-fade-up" style="display: flex; flex-direction: column; gap: 24px; max-width: 900px;">

    <!-- HEADER -->
    <div>
        <a href="{{ route('admin.pedidos.index') }}" style="color: #00AF9A; text-decoration: none; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 4px; margin-bottom: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
            Volver al listado
        </a>
        <h1 style="margin: 0; font-size: 2rem;">Nuevo Pedido de Reabastecimiento</h1>
        <p class="subtitle" style="margin: 4px 0 0 0;">Selecciona los productos que necesitan ser reabastecidos y la cantidad sugerida.</p>
    </div>

    <!-- ALERTA DE STOCK BAJO -->
    @if($stockBajo->count() > 0)
    <div style="background: #FEF3C7; border: 1px solid #FDE68A; border-radius: 12px; padding: 16px 20px;">
        <h3 style="margin: 0 0 8px 0; font-size: 0.95rem; color: #92400E;">⚠️ Productos con Stock Bajo (menos de 5 unidades)</h3>
        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
            @foreach($stockBajo as $prod)
                <span onclick="agregarProducto({{ $prod->idproducto }}, '{{ addslashes($prod->nombre) }}', {{ $prod->cantidad }})" style="background: white; border: 1px solid #FDE68A; padding: 6px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: background 0.2s; color: #92400E;" onmouseover="this.style.background='#FEF9C3'" onmouseout="this.style.background='white'">
                    {{ $prod->nombre }} <span style="color: #DC2626; font-weight: 900;">({{ $prod->cantidad }})</span>
                </span>
            @endforeach
        </div>
    </div>
    @endif

    <!-- SELECTOR DE PRODUCTOS -->
    <div class="card" style="background: white; border-radius: 12px; border: 1px solid var(--border); padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <h3 style="margin: 0 0 16px 0; font-size: 1.1rem;">Agregar Productos al Pedido</h3>

        <div style="display: flex; gap: 12px; align-items: flex-end; margin-bottom: 16px;">
            <div style="flex: 1;">
                <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Producto</label>
                <select id="producto-select" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem;">
                    <option value="">Seleccionar producto...</option>
                    @foreach($productos as $prod)
                        <option value="{{ $prod->idproducto }}" data-nombre="{{ $prod->nombre }}" data-stock="{{ $prod->cantidad }}">
                            {{ $prod->nombre }} (Stock: {{ $prod->cantidad }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="width: 120px;">
                <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Cantidad</label>
                <input type="number" id="cantidad-input" min="1" value="10" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;">
            </div>
            <button onclick="agregarDesdeSelect()" style="background: #00AF9A; color: white; border: none; padding: 10px 16px; border-radius: 8px; font-weight: 700; cursor: pointer; white-space: nowrap;">
                + Agregar
            </button>
        </div>

        <!-- TABLA DE PRODUCTOS SELECCIONADOS -->
        <div style="overflow-x: auto;">
            <table id="pedido-tabla" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border); text-align: left; background: #F9FAFB;">
                        <th style="padding: 10px 12px; font-weight: 800; color: #374151;">Producto</th>
                        <th style="padding: 10px 12px; font-weight: 800; color: #374151; text-align: center;">Stock Actual</th>
                        <th style="padding: 10px 12px; font-weight: 800; color: #374151; text-align: center;">Cant. Sugerida</th>
                        <th style="padding: 10px 12px; font-weight: 800; color: #374151; text-align: center;">Quitar</th>
                    </tr>
                </thead>
                <tbody id="pedido-body">
                    <tr id="empty-row">
                        <td colspan="4" style="padding: 20px; text-align: center; color: var(--text-light);">No has agregado productos aún.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">
            <label style="font-weight: 700; font-size: 0.85rem; color: #374151; display: block; margin-bottom: 6px;">Observaciones (opcional)</label>
            <textarea id="observaciones" rows="2" placeholder="Notas adicionales para el pedido..." style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; box-sizing: border-box; resize: vertical;"></textarea>
        </div>

        <div style="margin-top: 24px; text-align: right;">
            <button onclick="enviarPedido()" style="background: #00AF9A; color: white; border: none; padding: 12px 28px; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer;">
                Registrar Pedido
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let productosSeleccionados = [];

    function agregarDesdeSelect() {
        const sel = document.getElementById('producto-select');
        const opt = sel.options[sel.selectedIndex];
        if (!sel.value) return;

        const id = parseInt(sel.value);
        const nombre = opt.dataset.nombre;
        const stock = parseInt(opt.dataset.stock);
        const cantidad = parseInt(document.getElementById('cantidad-input').value) || 10;

        agregarProducto(id, nombre, stock, cantidad);
    }

    function agregarProducto(id, nombre, stock, cantidad = 10) {
        // Evitar duplicados
        if (productosSeleccionados.find(p => p.id === id)) {
            alert('Este producto ya está en la lista.');
            return;
        }

        productosSeleccionados.push({ id, nombre, stock, cantidad });
        renderTabla();
    }

    function quitarProducto(id) {
        productosSeleccionados = productosSeleccionados.filter(p => p.id !== id);
        renderTabla();
    }

    function renderTabla() {
        const tbody = document.getElementById('pedido-body');

        if (productosSeleccionados.length === 0) {
            tbody.innerHTML = '<tr id="empty-row"><td colspan="4" style="padding: 20px; text-align: center; color: var(--text-light);">No has agregado productos aún.</td></tr>';
            return;
        }

        tbody.innerHTML = '';
        productosSeleccionados.forEach(p => {
            const stockClass = p.stock < 5 ? 'color: #DC2626; font-weight: 900;' : '';
            tbody.innerHTML += `
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 10px 12px; font-weight: 600;">${p.nombre}</td>
                    <td style="padding: 10px 12px; text-align: center; ${stockClass}">${p.stock}</td>
                    <td style="padding: 10px 12px; text-align: center;">
                        <input type="number" min="1" value="${p.cantidad}" onchange="actualizarCantidad(${p.id}, this.value)" style="width: 80px; padding: 6px; border: 1px solid var(--border); border-radius: 6px; text-align: center;">
                    </td>
                    <td style="padding: 10px 12px; text-align: center;">
                        <button onclick="quitarProducto(${p.id})" style="background: #FEF2F2; color: #EF4444; border: 1px solid #FECACA; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-weight: 700; font-size: 0.8rem;">✕</button>
                    </td>
                </tr>
            `;
        });
    }

    function actualizarCantidad(id, val) {
        const prod = productosSeleccionados.find(p => p.id === id);
        if (prod) prod.cantidad = parseInt(val) || 1;
    }

    function enviarPedido() {
        if (productosSeleccionados.length === 0) {
            alert('Agrega al menos un producto al pedido.');
            return;
        }

        const payload = {
            productos: productosSeleccionados.map(p => ({ id: p.id, cantidad: p.cantidad })),
            observaciones: document.getElementById('observaciones').value,
        };

        fetch('{{ route("admin.pedidos.store") }}', {
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
                window.location.href = '{{ route("admin.pedidos.index") }}';
            } else {
                alert(data.message || 'Error al registrar el pedido.');
            }
        })
        .catch(e => alert('Error de conexión.'));
    }
</script>
@endpush
@endsection
