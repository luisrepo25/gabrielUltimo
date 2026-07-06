@extends('layouts.ferreteria')

@section('title', 'Gestionar Proveedores - Ferretería Guisella')

@section('content')
<div x-data="proveedorApp()" class="animate-fade-up" style="display: flex; flex-direction: column; gap: 24px;">
    
    <!-- HEADER -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="margin: 0; font-size: 2rem;">Gestionar Proveedores</h1>
            <p class="subtitle" style="margin: 4px 0 0 0;">Administra los distribuidores de mercadería y consulta sus notas de compra.</p>
        </div>
        <button @click="openAddModal()" class="btn-productos" style="display: flex; align-items: center; gap: 8px; font-weight: 700;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Añadir Proveedor
        </button>
    </div>

    <!-- MENSAJES DE ÉXITO O ERROR -->
    @if (session('success'))
        <div class="alert alert-success" style="margin: 0;">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-error" style="margin: 0;">
            {{ session('error') }}
        </div>
    @endif

    <!-- PANEL PRINCIPAL - LISTA Y DETALLE -->
    <div class="grid-main" style="display: grid; grid-template-columns: 1fr; gap: 24px; min-height: 500px;">
        
        <!-- PANEL IZQUIERDO: LISTADO -->
        <div class="card" style="background: white; border-radius: 12px; border: 1px solid var(--border); padding: 20px; display: flex; flex-direction: column; gap: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
            
            <!-- BUSCADOR -->
            <div class="search-container" style="width: 100%; max-width: none; margin: 0;">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <input type="text" x-model="searchQuery" class="search-input" placeholder="Buscar proveedor por nombre o CI..." style="width: 100%;">
            </div>

            <!-- LISTA -->
            <div style="display: flex; flex-direction: column; gap: 12px; max-height: 550px; overflow-y: auto; padding-right: 4px;">
                <template x-for="prov in filteredProveedores()" :key="prov.ci">
                    <div @click="selectProveedor(prov)" 
                         class="supplier-item"
                         :class="selectedProveedor && selectedProveedor.ci === prov.ci ? 'active-supplier' : ''"
                         style="padding: 14px; border-radius: 8px; border: 1px solid var(--border); cursor: pointer; transition: all 0.2s ease; display: flex; flex-direction: column; gap: 6px;">
                        
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <span style="font-weight: 800; font-size: 1.05rem;" x-text="prov.nombre"></span>
                            <span style="font-size: 0.75rem; background: rgba(0, 175, 154, 0.15); color: #00AF9A; padding: 2px 6px; border-radius: 4px; font-weight: 700; border: 1px solid rgba(0, 175, 154, 0.25);" x-text="'CI/NIT: ' + prov.ci"></span>
                        </div>
                        
                        <div style="display: flex; flex-direction: column; gap: 2px; font-size: 0.85rem; color: var(--text-light);">
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                <span x-text="prov.telefono"></span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 6px;" x-show="prov.correo">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                <span x-text="prov.correo"></span>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Estado Vacío del Buscador -->
                <div x-show="filteredProveedores().length === 0" style="text-align: center; padding: 40px 20px; color: var(--text-light);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 12px; opacity: 0.5;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <p style="margin: 0; font-weight: 600;">No se encontraron proveedores</p>
                </div>
            </div>
        </div>

        <!-- PANEL DERECHO: DETALLE & COMPRAS -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            
            <!-- CASO A: NO HAY SELECCIONADO -->
            <div x-show="!selectedProveedor" 
                 class="card" 
                 style="background: white; border-radius: 12px; border: 1px solid var(--border); padding: 50px 30px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 16px; min-height: 350px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                <div style="background: rgba(0, 175, 154, 0.08); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #00AF9A;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                </div>
                <h3 style="margin: 0; font-size: 1.25rem; font-weight: 800;">Ver Ficha de Proveedor</h3>
                <p style="margin: 0; color: var(--text-light); max-width: 320px; line-height: 1.5; font-size: 0.9rem;">Selecciona un proveedor de la lista de la izquierda para ver su información detallada, editar su ficha y ver su historial de compras.</p>
            </div>

            <!-- CASO B: SELECCIONADO -->
            <div x-show="selectedProveedor" x-transition.opacity style="display: flex; flex-direction: column; gap: 24px;">
                
                <!-- FICHA DEL PROVEEDOR -->
                <div class="card" style="background: white; border-radius: 12px; border: 1px solid var(--border); padding: 24px; display: flex; flex-direction: column; gap: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px; border-bottom: 1px solid var(--border); padding-bottom: 16px;">
                        <div>
                            <span style="font-size: 0.8rem; text-transform: uppercase; font-weight: 800; color: var(--text-light); tracking: 0.05em;">Ficha Técnica</span>
                            <h2 style="margin: 4px 0 0 0; font-size: 1.6rem; font-weight: 900;" x-text="selectedProveedor?.nombre"></h2>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button @click="openEditModal(selectedProveedor)" class="btn-productos" style="background: #F3F4F6; color: #1F2937; border: 1px solid var(--border); padding: 8px 14px; font-size: 0.85rem; font-weight: 700; display: flex; align-items: center; gap: 6px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4z"></path></svg>
                                Editar Ficha
                            </button>
                            
                            <!-- Botón de eliminación -->
                            <form :action="'/admin/proveedores/' + selectedProveedor?.ci" method="POST" @submit="return confirm('¿Está seguro de eliminar a este proveedor? Esta acción no se puede deshacer.')" style="margin: 0; display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-productos" style="background: rgba(239, 68, 68, 0.08); color: #EF4444; border: 1px solid rgba(239, 68, 68, 0.2); padding: 8px 14px; font-size: 0.85rem; font-weight: 700; display: flex; align-items: center; gap: 6px;" x-show="selectedProveedor?.compras.length === 0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Datos organizados en grilla -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                        <div>
                            <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-light); text-transform: uppercase;">CI / NIT del Proveedor</span>
                            <p style="margin: 4px 0 0 0; font-weight: 800; font-size: 1.1rem; color: #1F2937;" x-text="selectedProveedor?.ci"></p>
                        </div>
                        <div>
                            <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-light); text-transform: uppercase;">Teléfono de Contacto</span>
                            <p style="margin: 4px 0 0 0; font-weight: 800; font-size: 1.1rem; color: #1F2937;" x-text="selectedProveedor?.telefono"></p>
                        </div>
                        <div>
                            <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-light); text-transform: uppercase;">Correo Electrónico</span>
                            <p style="margin: 4px 0 0 0; font-weight: 600; font-size: 1rem; color: #1F2937;" x-text="selectedProveedor?.correo || 'Sin correo registrado'"></p>
                        </div>
                        <div>
                            <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-light); text-transform: uppercase;">Dirección de Suministro</span>
                            <p style="margin: 4px 0 0 0; font-weight: 600; font-size: 1rem; color: #1F2937;" x-text="selectedProveedor?.direccion || 'Sin dirección registrada'"></p>
                        </div>
                    </div>

                    <div>
                        <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-light); text-transform: uppercase;">Descripción o Categoría</span>
                        <p style="margin: 4px 0 0 0; color: #4B5563; font-size: 0.95rem; line-height: 1.5; white-space: pre-line;" x-text="selectedProveedor?.descripcion"></p>
                    </div>
                </div>

                <!-- HISTORIAL DE COMPRAS (ACORDEÓN) -->
                <div class="card" style="background: white; border-radius: 12px; border: 1px solid var(--border); padding: 24px; display: flex; flex-direction: column; gap: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 12px; margin-bottom: 4px;">
                        <h3 style="margin: 0; font-size: 1.25rem; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            Historial de Compras
                            <span style="background: #E5E7EB; color: #374151; font-size: 0.75rem; padding: 2px 8px; border-radius: 12px; font-weight: 700;" x-text="selectedProveedor?.compras.length || 0"></span>
                        </h3>
                    </div>

                    <!-- CASO HISTORIAL VACÍO -->
                    <div x-show="selectedProveedor?.compras.length === 0" style="text-align: center; padding: 40px 20px; color: var(--text-light);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 12px; opacity: 0.5;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                        <p style="margin: 0; font-weight: 600; font-size: 0.95rem;">No se han registrado notas de compra para este proveedor todavía</p>
                    </div>

                    <!-- ACORDEÓN DE COMPRAS -->
                    <div x-show="selectedProveedor?.compras.length > 0" style="display: flex; flex-direction: column; gap: 12px;">
                        <template x-for="compra in selectedProveedor?.compras" :key="compra.nro">
                            <div class="accordion-item" style="border: 1px solid var(--border); border-radius: 8px; overflow: hidden; background: #FAFAFA;">
                                
                                <!-- CABECERA ACORDEÓN -->
                                <div @click="toggleCompra(compra.nro)" 
                                     style="padding: 14px 16px; background: white; cursor: pointer; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; user-select: none; transition: background 0.2s;"
                                     :style="activeCompraId === compra.nro ? 'background: rgba(0, 175, 154, 0.02); border-bottom: 1px solid var(--border);' : ''">
                                    
                                    <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                                        <span style="font-weight: 800; font-size: 1rem; color: #1F2937;" x-text="'Nota Nro: ' + compra.nro"></span>
                                        <span style="font-size: 0.85rem; color: var(--text-light);" x-text="formatFecha(compra.fecha)"></span>
                                        <span style="font-size: 0.75rem; background: #EEF2F6; color: #4B5563; padding: 2px 8px; border-radius: 4px; font-weight: 700; border: 1px solid #DFE5EB;" x-text="compra.metodo_pago?.nombre || 'Pago Directo'"></span>
                                    </div>
                                    
                                    <div style="display: flex; align-items: center; gap: 16px;">
                                        <span style="font-weight: 900; font-size: 1.1rem; color: #00AF9A;" x-text="compra.total + ' Bs.'"></span>
                                        
                                        <!-- Flecha Rotativa -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                             style="transition: transform 0.2s;"
                                             :style="activeCompraId === compra.nro ? 'transform: rotate(180deg); color: #00AF9A;' : 'color: var(--text-light);'">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </div>
                                </div>

                                <!-- CONTENIDO ACORDEÓN DESPLEGABLE -->
                                <div x-show="activeCompraId === compra.nro" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     style="padding: 16px; background: white;">
                                    
                                    <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: var(--text-light); display: block; margin-bottom: 8px;">Detalle de Artículos Adquiridos</span>
                                    
                                    <div style="overflow-x: auto;">
                                        <table class="fg-table" style="width: 100%; border-collapse: collapse; min-width: 450px;">
                                            <thead>
                                                <tr style="border-bottom: 2px solid var(--border); text-align: left; background: #F9FAFB;">
                                                    <th style="padding: 10px 12px; font-size: 0.8rem; font-weight: 800; color: #4B5563;">Artículo / Material</th>
                                                    <th style="padding: 10px 12px; font-size: 0.8rem; font-weight: 800; color: #4B5563; text-align: center;">Cantidad</th>
                                                    <th style="padding: 10px 12px; font-size: 0.8rem; font-weight: 800; color: #4B5563; text-align: right;">Costo Unitario</th>
                                                    <th style="padding: 10px 12px; font-size: 0.8rem; font-weight: 800; color: #4B5563; text-align: right;">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-for="det in compra.detalles" :key="det.id_producto">
                                                    <tr style="border-bottom: 1px solid var(--border);">
                                                        <td style="padding: 10px 12px; font-size: 0.9rem; font-weight: 700; color: #1F2937;" x-text="det.producto?.nombre || 'Producto #' + det.id_producto"></td>
                                                        <td style="padding: 10px 12px; font-size: 0.9rem; text-align: center; color: #4B5563;" x-text="det.cantidad"></td>
                                                        <td style="padding: 10px 12px; font-size: 0.9rem; text-align: right; color: #4B5563;" x-text="det.precio_unitario + ' Bs.'"></td>
                                                        <td style="padding: 10px 12px; font-size: 0.9rem; text-align: right; font-weight: 700; color: #1F2937;" x-text="(parseFloat(det.precio_unitario) * parseInt(det.cantidad)).toFixed(2) + ' Bs.'"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════
         MODAL: AÑADIR PROVEEDOR
         ═══════════════════════════════════════════════════ -->
    <div x-show="showAddModal" 
         style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 20px;"
         x-transition.opacity>
         
        <div @click.away="showAddModal = false" 
             class="card" 
             style="background: white; border-radius: 12px; width: 100%; max-width: 550px; padding: 24px; display: flex; flex-direction: column; gap: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); max-height: 90vh; overflow-y: auto;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform scale-95 opacity-0"
             x-transition:enter-end="transform scale-100 opacity-100">
             
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 12px;">
                <h3 style="margin: 0; font-size: 1.25rem; font-weight: 800; color: #1F2937;">Añadir Nuevo Proveedor</h3>
                <button @click="showAddModal = false" style="background: none; border: none; cursor: pointer; color: var(--text-light);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <form action="{{ route('admin.proveedores.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 16px; margin: 0;">
                @csrf
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        <label for="ci" style="font-size: 0.8rem; font-weight: 800; color: #4B5563; text-transform: uppercase;">CI / NIT *</label>
                        <input type="number" name="ci" id="ci" required class="search-input" style="width: 100%; position: static;" placeholder="Ej: 8472910">
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        <label for="nombre" style="font-size: 0.8rem; font-weight: 800; color: #4B5563; text-transform: uppercase;">Nombre de Empresa *</label>
                        <input type="text" name="nombre" id="nombre" required class="search-input" style="width: 100%; position: static;" placeholder="Ej: Distribuidora Bosch">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        <label for="telefono" style="font-size: 0.8rem; font-weight: 800; color: #4B5563; text-transform: uppercase;">Teléfono de Contacto *</label>
                        <input type="number" name="telefono" id="telefono" required class="search-input" style="width: 100%; position: static;" placeholder="Ej: 78901234">
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        <label for="correo" style="font-size: 0.8rem; font-weight: 800; color: #4B5563; text-transform: uppercase;">Correo Electrónico</label>
                        <input type="email" name="correo" id="correo" class="search-input" style="width: 100%; position: static;" placeholder="Ej: ventas@bosch.com">
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <label for="direccion" style="font-size: 0.8rem; font-weight: 800; color: #4B5563; text-transform: uppercase;">Dirección Física</label>
                    <input type="text" name="direccion" id="direccion" class="search-input" style="width: 100%; position: static;" placeholder="Ej: Av. Blanco Galindo Km 4">
                </div>

                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <label for="descripcion" style="font-size: 0.8rem; font-weight: 800; color: #4B5563; text-transform: uppercase;">Descripción o Notas *</label>
                    <textarea name="descripcion" id="descripcion" required class="search-input" style="width: 100%; position: static; height: 100px; resize: vertical; padding: 10px;" placeholder="Detalla los materiales principales que provee..."></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid var(--border); padding-top: 16px; margin-top: 8px;">
                    <button type="button" @click="showAddModal = false" class="btn-productos" style="background: #F3F4F6; color: #4B5563; border: 1px solid var(--border); font-weight: 700;">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-productos" style="font-weight: 700;">
                        Registrar Proveedor
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════
         MODAL: EDITAR PROVEEDOR
         ═══════════════════════════════════════════════════ -->
    <div x-show="showEditModal" 
         style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 20px;"
         x-transition.opacity>
         
        <div @click.away="showEditModal = false" 
             class="card" 
             style="background: white; border-radius: 12px; width: 100%; max-width: 550px; padding: 24px; display: flex; flex-direction: column; gap: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); max-height: 90vh; overflow-y: auto;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform scale-95 opacity-0"
             x-transition:enter-end="transform scale-100 opacity-100">
             
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 12px;">
                <h3 style="margin: 0; font-size: 1.25rem; font-weight: 800; color: #1F2937;">Editar Proveedor</h3>
                <button @click="showEditModal = false" style="background: none; border: none; cursor: pointer; color: var(--text-light);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <form :action="'/admin/proveedores/' + editForm.ci" method="POST" style="display: flex; flex-direction: column; gap: 16px; margin: 0;">
                @csrf
                @method('PUT')
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        <label style="font-size: 0.8rem; font-weight: 800; color: #9CA3AF; text-transform: uppercase;">CI / NIT (No Editable)</label>
                        <input type="number" disabled :value="editForm.ci" class="search-input" style="width: 100%; position: static; background: #F3F4F6; cursor: not-allowed;">
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        <label for="edit_nombre" style="font-size: 0.8rem; font-weight: 800; color: #4B5563; text-transform: uppercase;">Nombre de Empresa *</label>
                        <input type="text" name="nombre" id="edit_nombre" required x-model="editForm.nombre" class="search-input" style="width: 100%; position: static;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        <label for="edit_telefono" style="font-size: 0.8rem; font-weight: 800; color: #4B5563; text-transform: uppercase;">Teléfono de Contacto *</label>
                        <input type="number" name="telefono" id="edit_telefono" required x-model="editForm.telefono" class="search-input" style="width: 100%; position: static;">
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        <label for="edit_correo" style="font-size: 0.8rem; font-weight: 800; color: #4B5563; text-transform: uppercase;">Correo Electrónico</label>
                        <input type="email" name="correo" id="edit_correo" x-model="editForm.correo" class="search-input" style="width: 100%; position: static;">
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <label for="edit_direccion" style="font-size: 0.8rem; font-weight: 800; color: #4B5563; text-transform: uppercase;">Dirección Física</label>
                    <input type="text" name="direccion" id="edit_direccion" x-model="editForm.direccion" class="search-input" style="width: 100%; position: static;">
                </div>

                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <label for="edit_descripcion" style="font-size: 0.8rem; font-weight: 800; color: #4B5563; text-transform: uppercase;">Descripción o Notas *</label>
                    <textarea name="descripcion" id="edit_descripcion" required x-model="editForm.descripcion" class="search-input" style="width: 100%; position: static; height: 100px; resize: vertical; padding: 10px;"></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid var(--border); padding-top: 16px; margin-top: 8px;">
                    <button type="button" @click="showEditModal = false" class="btn-productos" style="background: #F3F4F6; color: #4B5563; border: 1px solid var(--border); font-weight: 700;">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-productos" style="font-weight: 700;">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<!-- ESTILOS CSS INLINE EXCLUSIVOS PARA ESTA PANTALLA -->
<style>
    .grid-main {
        grid-template-columns: 2fr 3fr !important;
    }
    @media (max-width: 850px) {
        .grid-main {
            grid-template-columns: 1fr !important;
        }
    }
    
    .supplier-item {
        background: white;
    }
    .supplier-item:hover {
        background: #F9FAFB;
        border-color: #00AF9A !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 175, 154, 0.04);
    }
    
    .active-supplier {
        background: rgba(0, 175, 154, 0.03) !important;
        border-color: #00AF9A !important;
        box-shadow: 0 4px 8px rgba(0, 175, 154, 0.05) !important;
    }
    
    .accordion-item {
        transition: box-shadow 0.2s ease;
    }
    .accordion-item:hover {
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    }
</style>

<!-- LÓGICA ALPINEJS -->
<script>
    function proveedorApp() {
        return {
            proveedores: @json($proveedores),
            searchQuery: '',
            selectedProveedor: null,
            activeCompraId: null,
            
            // Control Modales
            showAddModal: false,
            showEditModal: false,
            
            // Formularios
            editForm: {
                ci: '',
                nombre: '',
                telefono: '',
                correo: '',
                direccion: '',
                descripcion: ''
            },
            
            init() {
                // Seleccionar automáticamente el primer proveedor si hay alguno en la lista
                if (this.proveedores.length > 0) {
                    this.selectProveedor(this.proveedores[0]);
                }
            },
            
            // Filtrar proveedores en tiempo real por búsqueda
            filteredProveedores() {
                if (this.searchQuery.trim() === '') {
                    return this.proveedores;
                }
                const q = this.searchQuery.toLowerCase();
                return this.proveedores.filter(p => 
                    p.nombre.toLowerCase().includes(q) || 
                    p.ci.toString().includes(q)
                );
            },
            
            // Seleccionar proveedor y restaurar acordeones
            selectProveedor(prov) {
                this.selectedProveedor = prov;
                this.activeCompraId = null; // Cerrar compras anteriores
                
                // Si el proveedor seleccionado tiene compras, expandir automáticamente la primera compra para mejor UX
                if (prov.compras && prov.compras.length > 0) {
                    this.activeCompraId = prov.compras[0].nro;
                }
            },
            
            // Desplegar/Colapsar acordeón de compra
            toggleCompra(nro) {
                if (this.activeCompraId === nro) {
                    this.activeCompraId = null;
                } else {
                    this.activeCompraId = nro;
                }
            },
            
            // Abrir y Limpiar Modal de Adición
            openAddModal() {
                this.showAddModal = true;
            },
            
            // Abrir Modal de Edición con Datos
            openEditModal(prov) {
                this.editForm = {
                    ci: prov.ci,
                    nombre: prov.nombre,
                    telefono: prov.telefono,
                    correo: prov.correo || '',
                    direccion: prov.direccion || '',
                    descripcion: prov.descripcion
                };
                this.showEditModal = true;
            },
            
            // Formateador de Fecha legible
            formatFecha(fechaStr) {
                if (!fechaStr) return '';
                const fecha = new Date(fechaStr);
                return fecha.toLocaleDateString('es-ES', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
        }
    }
</script>
@endsection
