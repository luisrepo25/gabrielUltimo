@extends('layouts.ferreteria')

@section('title', 'Control de Inventario - Ferretería Guisella')

@section('content')
<div class="animate-fade-up" style="display: flex; flex-direction: column; gap: 24px; max-width: 1400px; margin: 0 auto; padding-top: 10px;">
    
    {{-- Encabezado --}}
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="margin: 0; font-size: 1.8rem; font-weight: 900; color: #1F2937; display: flex; align-items: center; gap: 12px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#00AF9A" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                Control de Inventario
            </h1>
            <p style="margin: 6px 0 0 0; color: #6B7280; font-size: 0.95rem;">Supervisa tus existencias físicas, calcula el patrimonio y ajusta diferencias de stock.</p>
        </div>
    </div>

    {{-- Notificaciones --}}
    @if(session('success'))
        <div style="background: rgba(0, 175, 154, 0.1); border-left: 4px solid #00AF9A; padding: 16px; border-radius: 8px; color: #007668; font-weight: 600;">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error_acceso'))
        <div style="background: #FEE2E2; border-left: 4px solid #EF4444; padding: 16px; border-radius: 8px; color: #B91C1C; font-weight: 600;">
            ⚠️ {{ session('error_acceso') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background: #FEE2E2; border-left: 4px solid #EF4444; padding: 16px; border-radius: 8px; color: #B91C1C; font-weight: 600;">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- KPIs --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <!-- KPI: Valor Total -->
        <div class="card" style="padding: 24px; display: flex; align-items: center; gap: 20px; background: linear-gradient(135deg, #ffffff 0%, #f0fdfa 100%); border-bottom: 4px solid #00AF9A;">
            <div style="background: rgba(0, 175, 154, 0.1); padding: 16px; border-radius: 12px; color: #00AF9A;">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
            </div>
            <div>
                <h3 style="margin: 0; color: #6B7280; font-size: 0.9rem; text-transform: uppercase; font-weight: 700;">Patrimonio en Inventario</h3>
                <p style="margin: 4px 0 0 0; font-size: 1.8rem; font-weight: 900; color: #1F2937;">{{ number_format($valorTotalInventario, 2) }} Bs.</p>
            </div>
        </div>

        <!-- KPI: Total Productos -->
        <div class="card" style="padding: 24px; display: flex; align-items: center; gap: 20px; border-bottom: 4px solid #3B82F6;">
            <div style="background: rgba(59, 130, 246, 0.1); padding: 16px; border-radius: 12px; color: #3B82F6;">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
            </div>
            <div>
                <h3 style="margin: 0; color: #6B7280; font-size: 0.9rem; text-transform: uppercase; font-weight: 700;">Total de Referencias</h3>
                <p style="margin: 4px 0 0 0; font-size: 1.8rem; font-weight: 900; color: #1F2937;">{{ $totalProductos }} ítems</p>
            </div>
        </div>

        <!-- KPI: Stock Crítico -->
        <div class="card" style="padding: 24px; display: flex; align-items: center; gap: 20px; background: linear-gradient(135deg, #ffffff 0%, #fef2f2 100%); border-bottom: 4px solid #EF4444;">
            <div style="background: rgba(239, 68, 68, 0.1); padding: 16px; border-radius: 12px; color: #EF4444;">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            </div>
            <div>
                <h3 style="margin: 0; color: #6B7280; font-size: 0.9rem; text-transform: uppercase; font-weight: 700;">Productos en Stock Crítico</h3>
                <p style="margin: 4px 0 0 0; font-size: 1.8rem; font-weight: 900; color: {{ $productosCriticos > 0 ? '#EF4444' : '#1F2937' }};">
                    {{ $productosCriticos }} alertas
                </p>
            </div>
        </div>
    </div>

    {{-- Buscador Local (JS) --}}
    <div style="position: relative; margin-top: 10px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%);"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        <input type="text" id="filtroInventario" placeholder="Buscar producto por nombre, código o categoría..." onkeyup="filtrarTabla()" style="width: 100%; padding: 16px 16px 16px 48px; border: 2px solid #E5E7EB; border-radius: 12px; font-family: 'Inter', sans-serif; font-size: 1rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#00AF9A';" onblur="this.style.borderColor='#E5E7EB';">
    </div>

    {{-- Tabla de Inventario --}}
    <div class="card" style="padding: 0; overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; white-space: nowrap;" id="tablaInventario">
            <thead>
                <tr style="background: #F9FAFB; border-bottom: 2px solid #E5E7EB;">
                    <th style="padding: 16px; color: #4B5563; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">ID</th>
                    <th style="padding: 16px; color: #4B5563; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Producto</th>
                    <th style="padding: 16px; color: #4B5563; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Categoría</th>
                    <th style="padding: 16px; color: #4B5563; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; text-align: right;">Costo Base</th>
                    <th style="padding: 16px; color: #4B5563; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; text-align: right;">Precio Venta</th>
                    <th style="padding: 16px; color: #4B5563; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; text-align: center;">Stock Físico</th>
                    <th style="padding: 16px; color: #4B5563; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; text-align: center;">Estado</th>
                    <th style="padding: 16px; color: #4B5563; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; text-align: right;">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $prod)
                <tr style="border-bottom: 1px solid #E5E7EB; transition: background 0.2s;" class="fila-producto" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 16px; font-family: monospace; font-weight: 600; color: #6B7280;">#{{ str_pad($prod->idproducto, 4, '0', STR_PAD_LEFT) }}</td>
                    <td style="padding: 16px; font-weight: 600; color: #1F2937;" class="col-nombre">{{ $prod->nombre }}</td>
                    <td style="padding: 16px; color: #6B7280;" class="col-cat">{{ $prod->categoria->nombre ?? 'Sin categoría' }}</td>
                    
                    <td style="padding: 16px; text-align: right; color: #6B7280; font-weight: 500;">
                        @if(isset($prod->costo))
                            {{ number_format($prod->costo, 2) }} Bs.
                        @else
                            <span style="font-size: 0.8rem; background: #F3F4F6; padding: 2px 6px; border-radius: 4px;">N/A</span>
                        @endif
                    </td>
                    
                    <td style="padding: 16px; text-align: right; font-weight: 700; color: #00AF9A;">
                        {{ number_format($prod->precio, 2) }} Bs.
                    </td>

                    <td style="padding: 16px; text-align: center;">
                        <span style="font-size: 1.1rem; font-weight: 900; color: {{ $prod->cantidad <= 5 ? '#EF4444' : '#1F2937' }};">
                            {{ $prod->cantidad }}
                        </span>
                    </td>

                    <td style="padding: 16px; text-align: center;">
                        @if($prod->cantidad <= 0)
                            <span style="background: #FEE2E2; color: #B91C1C; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;">Agotado</span>
                        @elseif($prod->cantidad <= 5)
                            <span style="background: #FEF3C7; color: #B45309; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;">Stock Crítico</span>
                        @else
                            <span style="background: #D1FAE5; color: #047857; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;">Normal</span>
                        @endif
                    </td>

                    <td style="padding: 16px; text-align: right;">
                        <button type="button" 
                                onclick="abrirModalAjuste({{ $prod->idproducto }}, '{{ htmlspecialchars($prod->nombre) }}', {{ $prod->cantidad }})"
                                style="background: white; border: 2px solid #E5E7EB; padding: 8px 12px; border-radius: 8px; font-family: 'Inter', sans-serif; font-weight: 700; font-size: 0.85rem; color: #374151; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;"
                                onmouseover="this.style.borderColor='#00AF9A'; this.style.color='#00AF9A';"
                                onmouseout="this.style.borderColor='#E5E7EB'; this.style.color='#374151';">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                            Ajustar
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="padding: 40px; text-align: center; color: #6B7280;">No se encontraron productos en el inventario.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL DE AJUSTE --}}
<div id="modalAjuste" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; width: 100%; max-width: 500px; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); overflow: hidden; animation: modalIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
        <div style="padding: 24px; border-bottom: 1px solid #E5E7EB; display: flex; justify-content: space-between; align-items: center; background: #F9FAFB;">
            <h2 style="margin: 0; font-size: 1.25rem; font-weight: 800; color: #1F2937; display: flex; align-items: center; gap: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#00AF9A" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                Ajustar Stock Físico
            </h2>
            <button onclick="cerrarModalAjuste()" style="background: none; border: none; cursor: pointer; color: #6B7280; padding: 4px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        
        <form action="{{ route('admin.inventario.ajustar') }}" method="POST" style="padding: 24px;">
            @csrf
            <input type="hidden" id="input_idproducto" name="idproducto" value="">
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #4B5563; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Producto</label>
                <div id="display_nombre_producto" style="padding: 12px 16px; background: #F3F4F6; border-radius: 8px; color: #1F2937; font-weight: 700; font-size: 1rem;"></div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #4B5563; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Stock Actual (Sistema)</label>
                    <input type="text" id="display_stock_actual" disabled style="width: 100%; padding: 12px; border: 2px solid #E5E7EB; border-radius: 8px; background: #F9FAFB; color: #6B7280; font-weight: 700; font-size: 1.1rem; text-align: center; box-sizing: border-box; outline: none;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #00AF9A; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Nuevo Stock (Real)</label>
                    <input type="number" name="nueva_cantidad" id="input_nueva_cantidad" required min="0" style="width: 100%; padding: 12px; border: 2px solid #00AF9A; border-radius: 8px; background: white; color: #1F2937; font-weight: 900; font-size: 1.2rem; text-align: center; box-sizing: border-box; outline: none; box-shadow: 0 0 0 4px rgba(0, 175, 154, 0.1);">
                </div>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; font-weight: 700; color: #4B5563; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">
                    Motivo de Ajuste
                    <span style="font-size: 0.7rem; color: #EF4444;">* Obligatorio para Auditoría</span>
                </label>
                <textarea name="motivo" required minlength="5" maxlength="255" placeholder="Ej: Mercancía dañada por humedad, conteo físico manual difiere, pérdida en almacén..." style="width: 100%; padding: 12px; border: 2px solid #E5E7EB; border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 0.95rem; box-sizing: border-box; outline: none; resize: vertical; min-height: 80px;" onfocus="this.style.borderColor='#3B82F6';" onblur="this.style.borderColor='#E5E7EB';"></textarea>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="cerrarModalAjuste()" style="padding: 12px 24px; border-radius: 8px; font-weight: 700; background: white; color: #4B5563; border: 2px solid #E5E7EB; cursor: pointer;">Cancelar</button>
                <button type="submit" style="padding: 12px 24px; border-radius: 8px; font-weight: 700; background: #00AF9A; color: white; border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(0, 175, 154, 0.3);">Procesar Ajuste</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<style>
    @keyframes modalIn {
        from { opacity: 0; transform: translateY(20px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
</style>
<script>
    function filtrarTabla() {
        let input = document.getElementById("filtroInventario");
        let filter = input.value.toLowerCase();
        let table = document.getElementById("tablaInventario");
        let tr = table.getElementsByClassName("fila-producto");

        for (let i = 0; i < tr.length; i++) {
            let tdNombre = tr[i].getElementsByClassName("col-nombre")[0];
            let tdCat = tr[i].getElementsByClassName("col-cat")[0];
            
            if (tdNombre || tdCat) {
                let txtNombre = tdNombre.textContent || tdNombre.innerText;
                let txtCat = tdCat.textContent || tdCat.innerText;
                if (txtNombre.toLowerCase().indexOf(filter) > -1 || txtCat.toLowerCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }

    function abrirModalAjuste(id, nombre, stockActual) {
        document.getElementById('input_idproducto').value = id;
        document.getElementById('display_nombre_producto').textContent = nombre;
        document.getElementById('display_stock_actual').value = stockActual;
        
        let inputNueva = document.getElementById('input_nueva_cantidad');
        inputNueva.value = stockActual;
        
        document.getElementById('modalAjuste').style.display = 'flex';
        inputNueva.focus();
        inputNueva.select();
    }

    function cerrarModalAjuste() {
        document.getElementById('modalAjuste').style.display = 'none';
    }
</script>
@endpush
