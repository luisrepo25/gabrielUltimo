@extends('layouts.ferreteria')

@section('title', 'Punto de Venta (POS)')

@section('content')
<div class="pos-container animate-fade-in" x-data="posApp()" style="margin-top: 20px; display: grid; grid-template-columns: 1.6fr 1fr; gap: 24px; align-items: start; min-height: 80vh;">
    
    <!-- LADO IZQUIERDO: SELECCIÓN DE PRODUCTOS -->
    <div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 24px;">
        <h2 style="font-size: 1.4rem; font-weight: 700; color: #1e293b; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
            Detalle de los Productos
        </h2>

        <!-- Selector/Buscador de Producto -->
        <div style="margin-bottom: 24px;">
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #64748b; margin-bottom: 8px;">Buscar y Agregar Producto</label>
            <div style="display: flex; gap: 12px;">
                <div style="flex: 1; position: relative;">
                    <select class="form-control" x-model="selectedProductoId" @change="agregarProductoSeleccionado()" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; transition: border-color 0.2s;">
                        <option value="">-- Selecciona un producto para añadir --</option>
                        @foreach($productos as $prod)
                            <option value="{{ $prod->idproducto }}" data-nombre="{{ $prod->nombre }}" data-precio="{{ $prod->precio }}" data-stock="{{ $prod->cantidad }}">
                                {{ $prod->nombre }} - {{ number_format($prod->precio, 2) }} BOB (Stock: {{ $prod->cantidad }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Tabla de Productos Agregados -->
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid #f1f5f9; color: #64748b; font-size: 0.85rem; font-weight: 600;">
                        <th style="padding: 12px 8px; width: 45%;">Producto</th>
                        <th style="padding: 12px 8px; text-align: right; width: 15%;">P. Unit (Bs.)</th>
                        <th style="padding: 12px 8px; text-align: center; width: 15%;">Cantidad</th>
                        <th style="padding: 12px 8px; text-align: right; width: 15%;">Desct. (Bs.)</th>
                        <th style="padding: 12px 8px; text-align: right; width: 15%;">Subtotal</th>
                        <th style="padding: 12px 8px; text-align: center; width: 10%;"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in carrito" :key="item.id">
                        <tr style="border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; color: #334155;">
                            <td style="padding: 12px 8px;">
                                <div style="font-weight: 600;" x-text="item.nombre"></div>
                                <span style="font-size: 0.75rem; color: #94a3b8;" x-text="'Stock: ' + item.stock"></span>
                            </td>
                            <td style="padding: 12px 8px; text-align: right; font-weight: 500;" x-text="parseFloat(item.precio).toFixed(2)"></td>
                            <td style="padding: 12px 8px; text-align: center;">
                                <div style="display: inline-flex; align-items: center; border: 1px solid #cbd5e1; border-radius: 6px; overflow: hidden;">
                                    <button @click="disminuirCantidad(index)" type="button" style="border: none; background: #f8fafc; padding: 4px 10px; cursor: pointer; font-weight: bold; font-size: 1.1rem; color: #475569;">-</button>
                                    <input type="number" min="1" :max="item.stock" x-model.number="item.cantidad" @input="validarCantidad(index)" style="width: 50px; text-align: center; border: none; font-weight: 600; outline: none; -moz-appearance: textfield;">
                                    <button @click="aumentarCantidad(index)" type="button" style="border: none; background: #f8fafc; padding: 4px 10px; cursor: pointer; font-weight: bold; font-size: 1.1rem; color: #475569;">+</button>
                                </div>
                            </td>
                            <td style="padding: 12px 8px; text-align: right;">
                                <input type="number" min="0" :max="item.precio * item.cantidad" step="0.01" x-model.number="item.descuento" @input="validarDescuento(index)" style="width: 70px; text-align: right; padding: 6px; border: 1px solid #cbd5e1; border-radius: 6px; font-weight: 500; outline: none;">
                            </td>
                            <td style="padding: 12px 8px; text-align: right; font-weight: 700; color: #0f172a;" x-text="calcularSubtotal(item).toFixed(2)"></td>
                            <td style="padding: 12px 8px; text-align: center;">
                                <button @click="eliminarItem(index)" type="button" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 4px; display: inline-flex; border-radius: 4px; transition: background 0.2s;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='transparent'">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="carrito.length === 0">
                        <td colspan="6" style="padding: 32px; text-align: center; color: #94a3b8; font-style: italic;">
                            No hay productos agregados a la venta.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- LADO DERECHO: DATOS DE VENTA Y CONFIRMACIÓN -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        
        <!-- DATOS DEL CLIENTE -->
        <div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 24px;">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
                <span style="display: flex; align-items: center; gap: 8px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    Cliente
                </span>
                <button type="button" @click="abrirModalRegistro()" style="background: none; border: none; color: var(--primary); font-size: 0.85rem; font-weight: 600; cursor: pointer; text-decoration: underline;">
                    + Registrar Nuevo
                </button>
            </h2>

            <!-- Búsqueda por CI -->
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #64748b; margin-bottom: 6px;">Buscar por Cédula de Identidad (CI)</label>
                <div style="display: flex; gap: 8px;">
                    <input type="number" x-model="buscarCi" placeholder="Ej: 1234567" style="flex: 1; padding: 10px; border-radius: 6px; border: 1px solid #cbd5e1; outline: none; font-weight: 500;" @keydown.enter.prevent="buscarClientePorCi()">
                    <button type="button" @click="buscarClientePorCi()" style="background: #1e293b; color: white; border: none; padding: 10px 16px; border-radius: 6px; cursor: pointer; font-weight: 600; display: flex; align-items: center; justify-content: center;" :disabled="cargandoCliente">
                        <span x-show="!cargandoCliente">Buscar</span>
                        <svg x-show="cargandoCliente" style="animation: spin 1s linear infinite; width: 18px; height: 18px; display: none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle style="opacity: .25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path style="opacity: .75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Ficha del Cliente Cargado -->
            <div x-show="cliente" style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 16px; position: relative; display: none;" x-transition>
                <button type="button" @click="limpiarCliente()" style="position: absolute; top: 12px; right: 12px; background: none; border: none; color: #ef4444; font-size: 0.8rem; font-weight: bold; cursor: pointer;">Quitar</button>
                <div style="font-weight: 700; color: #1e293b; font-size: 1rem;" x-text="cliente?.nombre + ' ' + cliente?.apellido"></div>
                <div style="font-size: 0.85rem; color: #64748b; margin-top: 4px;" x-text="'CI: ' + cliente?.ci"></div>
                <div style="font-size: 0.85rem; color: #64748b;" x-text="'Telf: ' + (cliente?.telefono || 'No registrado')"></div>
                <div style="font-size: 0.85rem; color: #64748b;" x-text="'Email: ' + cliente?.email"></div>
            </div>
            
            <div x-show="!cliente" style="text-align: center; padding: 20px; color: #94a3b8; font-style: italic; border: 1px dashed #e2e8f0; border-radius: 8px; background: #fafafa;">
                Ningún cliente seleccionado para esta factura.
            </div>
        </div>

        <!-- MÉTODOS DE PAGO Y RESUMEN -->
        <div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 24px;">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin-bottom: 20px;">Resumen de la Venta</h2>

            <!-- Método de Pago -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #64748b; margin-bottom: 8px;">Método de Pago</label>
                <select x-model="metodoPagoId" class="form-control" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #cbd5e1; outline: none; font-weight: 500;">
                    @foreach($metodosPago as $metodo)
                        <option value="{{ $metodo->id }}">{{ $metodo->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Resumen numérico -->
            <div style="border-top: 1px solid #f1f5f9; padding-top: 16px; display: flex; flex-direction: column; gap: 8px; margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem; color: #64748b;">
                    <span>Total Descuentos</span>
                    <span style="font-weight: 600;" x-text="calcularTotalDescuento().toFixed(2) + ' BOB'"></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 1.2rem; color: #0f172a; font-weight: 800; border-top: 1px solid #e2e8f0; padding-top: 12px;">
                    <span>Total a Pagar</span>
                    <span style="color: var(--primary);" x-text="calcularTotalGeneral().toFixed(2) + ' BOB'"></span>
                </div>
            </div>

            <!-- Botón procesar venta -->
            <button type="button" @click="procesarVenta()" style="width: 100%; background: var(--primary); color: white; border: none; padding: 14px; border-radius: 8px; font-size: 1.05rem; font-weight: 700; cursor: pointer; transition: background 0.2s; box-shadow: 0 4px 12px rgba(255, 107, 0, 0.2);" :disabled="procesandoVenta" onmouseover="this.style.background='var(--primary-hover)'" onmouseout="this.style.background='var(--primary)'">
                <span x-show="!procesandoVenta">Registrar y Generar Comprobante</span>
                <svg x-show="procesandoVenta" style="animation: spin 1s linear infinite; width: 20px; height: 20px; display: none; margin: 0 auto;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle style="opacity: .25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path style="opacity: .75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </button>
        </div>
    </div>

    <!-- MODAL DE REGISTRO RÁPIDO DE CLIENTES -->
    <div x-show="modalClienteAbierto" style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999; backdrop-filter: blur(4px); display: none;" x-transition>
        <div @click.away="cerrarModalRegistro()" style="background: white; border-radius: 12px; width: 100%; max-width: 500px; padding: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0;">Registrar Nuevo Cliente</h3>
                <button type="button" @click="cerrarModalRegistro()" style="background: none; border: none; font-size: 1.5rem; color: #94a3b8; cursor: pointer;">&times;</button>
            </div>

            <form @submit.prevent="guardarNuevoCliente()">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div style="margin-bottom: 12px; grid-column: span 2;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #64748b; margin-bottom: 4px;">Cédula de Identidad (CI) *</label>
                        <input type="number" x-model="nuevoCliente.ci" required style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1; outline: none;">
                    </div>
                    <div style="margin-bottom: 12px;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #64748b; margin-bottom: 4px;">Nombre *</label>
                        <input type="text" x-model="nuevoCliente.nombre" required style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1; outline: none;">
                    </div>
                    <div style="margin-bottom: 12px;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #64748b; margin-bottom: 4px;">Apellido *</label>
                        <input type="text" x-model="nuevoCliente.apellido" required style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1; outline: none;">
                    </div>
                    <div style="margin-bottom: 12px;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #64748b; margin-bottom: 4px;">Sexo *</label>
                        <select x-model="nuevoCliente.sexo" required style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1; outline: none; background: white;">
                            <option value="">Seleccione...</option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #64748b; margin-bottom: 4px;">Teléfono</label>
                        <input type="number" x-model="nuevoCliente.telefono" style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1; outline: none;">
                    </div>
                    <div style="margin-bottom: 12px; grid-column: span 2;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #64748b; margin-bottom: 4px;">Correo Electrónico *</label>
                        <input type="email" x-model="nuevoCliente.email" required style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1; outline: none;">
                    </div>
                    <div style="margin-bottom: 16px; grid-column: span 2;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #64748b; margin-bottom: 4px;">Domicilio</label>
                        <input type="text" x-model="nuevoCliente.domicilio" style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1; outline: none;">
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px;">
                    <button type="button" @click="cerrarModalRegistro()" style="background: #f1f5f9; color: #475569; border: none; padding: 10px 16px; border-radius: 6px; cursor: pointer; font-weight: 600;">Cancelar</button>
                    <button type="submit" style="background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600;" :disabled="guardandoCliente">
                        <span x-show="!guardandoCliente">Registrar</span>
                        <svg x-show="guardandoCliente" style="animation: spin 1s linear infinite; width: 18px; height: 18px; display: none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle style="opacity: .25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path style="opacity: .75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endsection

@push('scripts')
<script>
    function posApp() {
        return {
            selectedProductoId: '',
            buscarCi: '',
            cliente: null,
            metodoPagoId: '1',
            carrito: [],
            cargandoCliente: false,
            procesandoVenta: false,
            modalClienteAbierto: false,
            guardandoCliente: false,
            nuevoCliente: {
                ci: '',
                nombre: '',
                apellido: '',
                sexo: '',
                telefono: '',
                email: '',
                domicilio: ''
            },

            agregarProductoSeleccionado() {
                if (!this.selectedProductoId) return;

                const select = document.querySelector('select');
                const selectedOption = select.options[select.selectedIndex];
                
                const id = this.selectedProductoId;
                const nombre = selectedOption.getAttribute('data-nombre');
                const precio = parseFloat(selectedOption.getAttribute('data-precio'));
                const stock = parseInt(selectedOption.getAttribute('data-stock'));

                // Verificar si ya existe en el carrito
                const existente = this.carrito.find(item => item.id == id);
                if (existente) {
                    if (existente.cantidad < stock) {
                        existente.cantidad += 1;
                    } else {
                        showToast('No puedes agregar más de este producto. Stock alcanzado.', 'error');
                    }
                } else {
                    this.carrito.push({
                        id: id,
                        nombre: nombre,
                        precio: precio,
                        cantidad: 1,
                        stock: stock,
                        descuento: 0
                    });
                }

                // Resetear selector
                this.selectedProductoId = '';
            },

            aumentarCantidad(index) {
                const item = this.carrito[index];
                if (item.cantidad < item.stock) {
                    item.cantidad++;
                } else {
                    showToast('Se ha alcanzado el límite de stock disponible.', 'error');
                }
            },

            disminuirCantidad(index) {
                const item = this.carrito[index];
                if (item.cantidad > 1) {
                    item.cantidad--;
                } else {
                    this.eliminarItem(index);
                }
            },

            validarCantidad(index) {
                const item = this.carrito[index];
                if (item.cantidad < 1) item.cantidad = 1;
                if (item.cantidad > item.stock) {
                    item.cantidad = item.stock;
                    showToast('Excede el stock disponible', 'error');
                }
            },

            validarDescuento(index) {
                const item = this.carrito[index];
                const maxDesct = item.precio * item.cantidad;
                if (item.descuento < 0) item.descuento = 0;
                if (item.descuento > maxDesct) {
                    item.descuento = maxDesct;
                    showToast('El descuento no puede ser superior al subtotal', 'error');
                }
            },

            eliminarItem(index) {
                this.carrito.splice(index, 1);
            },

            calcularSubtotal(item) {
                return (item.precio * item.cantidad) - (item.descuento || 0);
            },

            calcularTotalDescuento() {
                return this.carrito.reduce((sum, item) => sum + (parseFloat(item.descuento) || 0), 0);
            },

            calcularTotalGeneral() {
                return this.carrito.reduce((sum, item) => sum + this.calcularSubtotal(item), 0);
            },

            buscarClientePorCi() {
                if (!this.buscarCi) return;
                this.cargandoCliente = true;

                fetch(`/api/clientes/buscar/${this.buscarCi}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Cliente no encontrado');
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            this.cliente = data.cliente;
                            showToast('Cliente cargado correctamente.');
                        } else {
                            showToast('Cliente no encontrado.', 'error');
                        }
                    })
                    .catch(error => {
                        showToast('Cliente no registrado en el sistema.', 'error');
                        this.cliente = null;
                    })
                    .finally(() => {
                        this.cargandoCliente = false;
                    });
            },

            limpiarCliente() {
                this.cliente = null;
                this.buscarCi = '';
            },

            abrirModalRegistro() {
                this.nuevoCliente = {
                    ci: this.buscarCi || '',
                    nombre: '',
                    apellido: '',
                    sexo: '',
                    telefono: '',
                    email: '',
                    domicilio: ''
                };
                this.modalClienteAbierto = true;
            },

            cerrarModalRegistro() {
                this.modalClienteAbierto = false;
            },

            guardarNuevoCliente() {
                this.guardandoCliente = true;

                fetch('/api/clientes/rapido', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.nuevoCliente)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.cliente = data.cliente;
                        this.buscarCi = data.cliente.ci;
                        this.modalClienteAbierto = false;
                        showToast('Cliente registrado y seleccionado con éxito.');
                    } else {
                        showToast(data.message || 'Error al registrar cliente.', 'error');
                    }
                })
                .catch(error => {
                    showToast('Ocurrió un error en el servidor.', 'error');
                    console.error(error);
                })
                .finally(() => {
                    this.guardandoCliente = false;
                });
            },

            procesarVenta() {
                if (!this.cliente) {
                    showToast('Debes seleccionar o registrar un cliente antes de continuar.', 'error');
                    return;
                }
                if (this.carrito.length === 0) {
                    showToast('El carrito de venta debe tener al menos un producto.', 'error');
                    return;
                }

                this.procesandoVenta = true;

                const payload = {
                    ci_cliente: this.cliente.ci,
                    id_pago: this.metodoPagoId,
                    productos: this.carrito.map(item => ({
                        id: item.id,
                        cantidad: item.cantidad,
                        descuento: item.descuento
                    }))
                };

                fetch('/ventas', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('¡Venta realizada con éxito!');
                        
                        // Descargar el comprobante PDF inmediatamente
                        window.location.href = `/ventas/comprobante/${data.nro_factura}`;

                        // Limpiar formulario tras 1 segundo
                        setTimeout(() => {
                            this.carrito = [];
                            this.cliente = null;
                            this.buscarCi = '';
                            this.metodoPagoId = '1';
                        }, 1000);
                    } else {
                        showToast(data.message || 'Error al procesar la venta.', 'error');
                    }
                })
                .catch(error => {
                    showToast('Ocurrió un error al enviar los datos.', 'error');
                    console.error(error);
                })
                .finally(() => {
                    this.procesandoVenta = false;
                });
            }
        };
    }
</script>
@endpush
