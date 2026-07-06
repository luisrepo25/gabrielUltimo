@extends('layouts.ferreteria')

@section('title', 'Registrar Compra - Ferretería Guisella')

@section('content')
<div x-data="compraApp()" class="animate-fade-up" style="display: flex; flex-direction: column; gap: 24px; max-width: 1300px; margin: 0 auto;">
    
    <!-- HEADER -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="margin: 0; font-size: 1.8rem; font-weight: 900; color: #1F2937;">
                <span style="color: #00AF9A;"></span> Registrar Compra
            </h1>
            <p style="margin: 6px 0 0 0; color: #6B7280; font-size: 0.9rem;">Ingresa los productos adquiridos para reabastecer el stock del inventario.</p>
        </div>
        <a href="{{ route('admin.compras.index') }}" style="background: white; color: #4B5563; border: 1px solid #E5E7EB; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 10px; font-size: 0.9rem; transition: all 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.05);"
           onmouseover="this.style.borderColor='#00AF9A'; this.style.color='#00AF9A';"
           onmouseout="this.style.borderColor='#E5E7EB'; this.style.color='#4B5563';">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Volver al Historial
        </a>
    </div>

    <!-- NOTIFICACIONES TOAST (Alpine.js) -->
    <div x-show="toast.show" 
         x-transition.opacity
         :style="toast.type === 'error' ? 'background: linear-gradient(135deg, #EF4444, #DC2626);' : 'background: linear-gradient(135deg, #00AF9A, #059669);'"
         style="position: fixed; bottom: 24px; right: 24px; color: white; padding: 14px 28px; border-radius: 12px; z-index: 99999; box-shadow: 0 8px 25px rgba(0,0,0,0.2); font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;"
         x-text="toast.message">
    </div>

    <!-- FORMULARIO DE COMPRA -->
    <div class="compra-grid" style="display: grid; grid-template-columns: 1fr 420px; gap: 24px; align-items: start;">
        
        <!-- ═══════════════════════════════════════════════════
             COLUMNA IZQUIERDA: LISTA DE ARTÍCULOS (CARRITO)
             ═══════════════════════════════════════════════════ -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            
            <div style="background: white; border-radius: 16px; border: 1px solid #E5E7EB; padding: 0; box-shadow: 0 4px 16px rgba(0,0,0,0.04); overflow: hidden;">
                <!-- Card Header -->
                <div style="padding: 20px 24px; border-bottom: 1px solid #F3F4F6; display: flex; align-items: center; gap: 10px; background: linear-gradient(135deg, #F9FAFB, #FFFFFF);">
                    <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #00AF9A, #059669); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #1F2937;">Lista de Artículos</h3>
                        <p style="margin: 0; font-size: 0.75rem; color: #9CA3AF;" x-text="cart.length + ' artículo(s) en la compra'"></p>
                    </div>
                </div>
                
                <!-- CASO: SIN PRODUCTOS -->
                <div x-show="cart.length === 0" style="text-align: center; padding: 60px 30px; color: #9CA3AF; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 16px;">
                    <div style="width: 72px; height: 72px; background: #F3F4F6; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                    </div>
                    <p style="margin: 0; font-weight: 700; color: #6B7280; font-size: 1rem;">Sin artículos en la compra</p>
                    <p style="margin: 0; font-size: 0.85rem; max-width: 280px; line-height: 1.5; color: #9CA3AF;">Busca y agrega productos desde el panel de <strong style="color: #6B7280;">Catálogo</strong> ubicado a la derecha.</p>
                </div>

                <!-- TABLA DINÁMICA -->
                <div x-show="cart.length > 0" style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; min-width: 560px;">
                        <thead>
                            <tr style="border-bottom: 2px solid #F3F4F6; text-align: left;">
                                <th style="padding: 12px 16px; font-weight: 800; color: #6B7280; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Artículo</th>
                                <th style="padding: 12px 16px; font-weight: 800; color: #6B7280; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; width: 110px;">Cant.</th>
                                <th style="padding: 12px 16px; font-weight: 800; color: #6B7280; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; text-align: right; width: 140px;">Costo Unit.</th>
                                <th style="padding: 12px 16px; font-weight: 800; color: #6B7280; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; text-align: right; width: 120px;">Subtotal</th>
                                <th style="padding: 12px 16px; width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in cart" :key="item.id">
                                <tr style="border-bottom: 1px solid #F3F4F6; transition: background 0.15s;" onmouseover="this.style.background='#FAFBFC'" onmouseout="this.style.background='white'">
                                    <td style="padding: 14px 16px;">
                                        <div style="font-weight: 700; color: #1F2937; font-size: 0.9rem;" x-text="item.nombre"></div>
                                        <div style="font-size: 0.75rem; color: #9CA3AF; font-weight: 500; margin-top: 2px;" x-text="'Stock actual: ' + item.stock_actual + ' • ID: ' + item.id"></div>
                                    </td>
                                    <td style="padding: 14px 8px; text-align: center;">
                                        <input type="number" 
                                               x-model.number="item.cantidad" 
                                               min="1" 
                                               @input="validateItem(index)"
                                               class="compra-input" 
                                               style="width: 70px; text-align: center; font-weight: 800;">
                                    </td>
                                    <td style="padding: 14px 8px; text-align: right;">
                                        <input type="number" 
                                               step="0.01" 
                                               x-model.number="item.precio_unitario" 
                                               min="0" 
                                               @input="validateItem(index)"
                                               class="compra-input" 
                                               style="width: 110px; text-align: right; font-weight: 800;" 
                                               placeholder="0.00">
                                    </td>
                                    <td style="padding: 14px 16px; text-align: right; font-weight: 900; color: #1F2937; font-size: 0.95rem; white-space: nowrap;" x-text="(item.cantidad * item.precio_unitario).toFixed(2) + ' Bs.'">
                                    </td>
                                    <td style="padding: 14px 8px; text-align: center;">
                                        <button @click="removeItem(index)" style="background: none; border: none; cursor: pointer; color: #EF4444; display: flex; align-items: center; justify-content: center; padding: 6px; border-radius: 8px; transition: background 0.2s;"
                                                onmouseover="this.style.background='#FEF2F2'" onmouseout="this.style.background='transparent'">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- MONTO TOTAL DE COMPRA -->
                <div x-show="cart.length > 0" style="display: flex; justify-content: flex-end; align-items: center; gap: 16px; padding: 20px 24px; background: linear-gradient(135deg, #F0FDF9, #ECFDF5); border-top: 2px solid #D1FAE5;">
                    <span style="font-weight: 700; font-size: 1rem; color: #6B7280;">Gran Total:</span>
                    <span style="font-weight: 900; font-size: 1.8rem; color: #059669; letter-spacing: -0.5px;" x-text="calculateTotal() + ' Bs.'"></span>
                </div>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════
             COLUMNA DERECHA: PROVEEDOR, CATÁLOGO Y ACCIONES
             ═══════════════════════════════════════════════════ -->
        <div style="display: flex; flex-direction: column; gap: 20px; position: sticky; top: 90px;">
            
            <!-- DATOS DE COMPRA (PROVEEDOR & PAGO) -->
            <div style="background: white; border-radius: 16px; border: 1px solid #E5E7EB; padding: 0; box-shadow: 0 4px 16px rgba(0,0,0,0.04); overflow: visible;">
                <!-- Card Header -->
                <div style="padding: 18px 20px; border-bottom: 1px solid #F3F4F6; display: flex; align-items: center; gap: 10px; background: linear-gradient(135deg, #F9FAFB, #FFFFFF); border-radius: 16px 16px 0 0;">
                    <div style="width: 32px; height: 32px; background: linear-gradient(135deg, #6366F1, #4F46E5); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </div>
                    <h3 style="margin: 0; font-size: 1rem; font-weight: 800; color: #1F2937;">Datos de Compra</h3>
                </div>
                
                <div style="padding: 20px; display: flex; flex-direction: column; gap: 16px;">
                    <!-- PROVEEDOR -->
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        <label style="font-size: 0.7rem; font-weight: 800; color: #6B7280; text-transform: uppercase; letter-spacing: 0.5px;">Proveedor *</label>
                        <div style="display: flex; gap: 8px;">
                            <!-- Autocomplete wrapper: input + dropdown juntos -->
                            <div style="position: relative; flex: 1;" @click.away="supplierDropdown = false">
                                <div style="position: relative;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); pointer-events: none;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                    <input type="text" 
                                           x-model="supplierSearch" 
                                           @focus="supplierDropdown = true; filterSuppliers();"
                                           @input="supplierDropdown = true; filterSuppliers();"
                                           class="compra-input" 
                                           style="width: 100%; padding-left: 36px;" 
                                           placeholder="Buscar por nombre o CI/NIT...">
                                </div>
                                
                                <!-- Dropdown de Proveedores -->
                                <div x-show="supplierDropdown && filteredSuppliersList.length > 0" 
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 transform -translate-y-1"
                                     x-transition:enter-end="opacity-100 transform translate-y-0"
                                     style="position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: white; border: 1px solid #E5E7EB; border-radius: 12px; box-shadow: 0 12px 28px rgba(0,0,0,0.12); z-index: 1000; max-height: 220px; overflow-y: auto;">
                                    <template x-for="s in filteredSuppliersList" :key="s.ci">
                                        <div @click="selectSupplier(s)" 
                                             style="padding: 10px 14px; cursor: pointer; transition: background 0.15s; font-size: 0.88rem; border-bottom: 1px solid #F9FAFB;"
                                             onmouseover="this.style.background='#F0FDF9'" onmouseout="this.style.background='white'">
                                            <span style="font-weight: 700; color: #1F2937;" x-text="s.nombre"></span>
                                            <div style="font-size: 0.72rem; color: #9CA3AF; margin-top: 1px;" x-text="'CI/NIT: ' + s.ci"></div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <button type="button" @click="showAddSupplierModal = true" 
                                    style="background: linear-gradient(135deg, #6366F1, #4F46E5); color: white; border: none; padding: 0 14px; border-radius: 10px; font-size: 0.85rem; font-weight: 700; white-space: nowrap; cursor: pointer; transition: transform 0.15s, box-shadow 0.15s; box-shadow: 0 2px 8px rgba(99,102,241,0.25);"
                                    onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(99,102,241,0.35)';"
                                    onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 8px rgba(99,102,241,0.25)';">
                                + Nuevo
                            </button>
                        </div>
                        
                        <!-- Ficha del Proveedor Seleccionado -->
                        <div x-show="selectedSupplier" x-transition
                             style="background: linear-gradient(135deg, #F0FDF9, #ECFDF5); border: 1px solid #D1FAE5; border-radius: 10px; padding: 12px 14px; margin-top: 4px; font-size: 0.85rem; display: flex; flex-direction: column; gap: 4px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-weight: 800; color: #059669; display: flex; align-items: center; gap: 6px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                    <span x-text="selectedSupplier?.nombre"></span>
                                </span>
                                <span style="font-size: 0.72rem; font-weight: 700; color: #6B7280; background: white; padding: 2px 8px; border-radius: 20px;" x-text="'CI/NIT: ' + selectedSupplier?.ci"></span>
                            </div>
                            <span style="color: #6B7280; font-size: 0.8rem;" x-text="'📞 ' + selectedSupplier?.telefono"></span>
                        </div>
                    </div>

                    <!-- MÉTODO DE PAGO (SOLO EFECTIVO) -->
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        <label style="font-size: 0.7rem; font-weight: 800; color: #6B7280; text-transform: uppercase; letter-spacing: 0.5px;">Método de Pago</label>
                        <div style="display: flex; align-items: center; gap: 10px; background: #F9FAFB; padding: 10px 14px; border-radius: 10px; border: 1px solid #E5E7EB;">
                            <div style="width: 28px; height: 28px; background: linear-gradient(135deg, #F59E0B, #D97706); border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                            </div>
                            <div>
                                <span style="font-weight: 800; color: #1F2937; font-size: 0.9rem;">Efectivo</span>
                                <p style="margin: 0; font-size: 0.72rem; color: #9CA3AF;">Control simplificado de caja</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BUSCADOR DE CATÁLOGO (PRODUCTOS) -->
            <div style="background: white; border-radius: 16px; border: 1px solid #E5E7EB; padding: 0; box-shadow: 0 4px 16px rgba(0,0,0,0.04); overflow: visible;">
                <!-- Card Header -->
                <div style="padding: 18px 20px; border-bottom: 1px solid #F3F4F6; display: flex; align-items: center; gap: 10px; background: linear-gradient(135deg, #F9FAFB, #FFFFFF); border-radius: 16px 16px 0 0;">
                    <div style="width: 32px; height: 32px; background: linear-gradient(135deg, #F59E0B, #D97706); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </div>
                    <h3 style="margin: 0; font-size: 1rem; font-weight: 800; color: #1F2937;">Catálogo de Artículos</h3>
                </div>
                
                <div style="padding: 20px; display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; gap: 8px;">
                        <!-- Autocomplete wrapper: input + dropdown juntos -->
                        <div style="position: relative; flex: 1;" @click.away="productDropdown = false">
                            <div style="position: relative;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); pointer-events: none;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                <input type="text" 
                                       x-model="productSearch" 
                                       @focus="productDropdown = true; filterProducts();"
                                       @input="productDropdown = true; filterProducts();"
                                       class="compra-input" 
                                       style="width: 100%; padding-left: 36px;" 
                                       placeholder="Buscar por nombre o ID...">
                            </div>
                            
                            <!-- Dropdown de Productos -->
                            <div x-show="productDropdown && filteredProductsList.length > 0" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 transform -translate-y-1"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 style="position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: white; border: 1px solid #E5E7EB; border-radius: 12px; box-shadow: 0 12px 28px rgba(0,0,0,0.12); z-index: 1000; max-height: 260px; overflow-y: auto;">
                                <template x-for="p in filteredProductsList" :key="p.idproducto">
                                    <div @click="addProductToCart(p)" 
                                         style="padding: 10px 14px; cursor: pointer; transition: background 0.15s; font-size: 0.88rem; border-bottom: 1px solid #F9FAFB; display: flex; justify-content: space-between; align-items: center;"
                                         onmouseover="this.style.background='#FFF7ED'" onmouseout="this.style.background='white'">
                                        <div>
                                            <span style="font-weight: 700; color: #1F2937;" x-text="p.nombre"></span>
                                            <div style="font-size: 0.72rem; color: #9CA3AF; margin-top: 1px;" x-text="'ID: ' + p.idproducto + ' • Stock: ' + p.cantidad"></div>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; opacity: 0.5;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <button type="button" @click="showAddProductModal = true" 
                                style="background: linear-gradient(135deg, #F59E0B, #D97706); color: white; border: none; padding: 0 14px; border-radius: 10px; font-size: 0.85rem; font-weight: 700; white-space: nowrap; cursor: pointer; transition: transform 0.15s, box-shadow 0.15s; box-shadow: 0 2px 8px rgba(245,158,11,0.25);"
                                onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(245,158,11,0.35)';"
                                onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 8px rgba(245,158,11,0.25)';">
                            + Nuevo
                        </button>
                    </div>
                    <p style="margin: 0; font-size: 0.72rem; color: #9CA3AF; line-height: 1.4;">Escribe para buscar en tu inventario o crea un producto nuevo con <strong>+ Nuevo</strong>.</p>
                </div>
            </div>

            <!-- PROCESAR COMPRA -->
            <button type="button" 
                    @click="processPurchase()"
                    :disabled="cart.length === 0 || !selectedSupplier || loading"
                    style="width: 100%; padding: 16px; font-size: 1.1rem; font-weight: 800; display: flex; align-items: center; justify-content: center; gap: 10px; border: none; border-radius: 14px; cursor: pointer; color: white; transition: all 0.25s;"
                    :style="(cart.length === 0 || !selectedSupplier || loading) 
                        ? 'background: #D1D5DB; cursor: not-allowed; box-shadow: none;' 
                        : 'background: linear-gradient(135deg, #00AF9A, #059669); box-shadow: 0 6px 20px rgba(0, 175, 154, 0.3);'"
                    onmouseover="if(!this.disabled){this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(0,175,154,0.4)';}"
                    onmouseout="this.style.transform='none'; if(!this.disabled){this.style.boxShadow='0 6px 20px rgba(0,175,154,0.3)';}">
                <svg x-show="!loading" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <span x-show="loading" style="display: flex; align-items: center; gap: 8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="animation: spin 1s linear infinite;"><circle cx="12" cy="12" r="10" stroke="white" stroke-width="3" fill="none" stroke-dasharray="31.416" stroke-dashoffset="10" stroke-linecap="round"></circle></svg>
                    Procesando...
                </span>
                <span x-show="!loading">Procesar Nota de Compra</span>
            </button>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════
         MODAL: REGISTRAR PROVEEDOR AL VUELO
         ═══════════════════════════════════════════════════ -->
    <div x-show="showAddSupplierModal" 
         style="position: fixed; inset: 0; background: rgba(0,0,0,0.45); backdrop-filter: blur(4px); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 20px;"
         x-transition.opacity>
         
        <div @click.away="showAddSupplierModal = false" 
             style="background: white; border-radius: 16px; width: 100%; max-width: 500px; padding: 0; display: flex; flex-direction: column; box-shadow: 0 20px 40px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="transform scale-95 opacity-0"
             x-transition:enter-end="transform scale-100 opacity-100">
             
            <!-- Modal Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #F3F4F6; background: linear-gradient(135deg, #EEF2FF, #E0E7FF); border-radius: 16px 16px 0 0;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 32px; height: 32px; background: linear-gradient(135deg, #6366F1, #4F46E5); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                    </div>
                    <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #1F2937;">Nuevo Proveedor</h3>
                </div>
                <button @click="showAddSupplierModal = false" style="background: none; border: none; cursor: pointer; color: #9CA3AF; padding: 4px; border-radius: 6px; transition: color 0.15s;" onmouseover="this.style.color='#4B5563'" onmouseout="this.style.color='#9CA3AF'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <!-- Formulario AJAX -->
            <div style="padding: 24px; display: flex; flex-direction: column; gap: 14px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div style="display: flex; flex-direction: column; gap: 4px;">
                        <label style="font-size: 0.7rem; font-weight: 800; color: #6B7280; text-transform: uppercase;">CI / NIT *</label>
                        <input type="number" x-model.number="supplierForm.ci" class="compra-input" placeholder="Ej: 9081223">
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 4px;">
                        <label style="font-size: 0.7rem; font-weight: 800; color: #6B7280; text-transform: uppercase;">Empresa / Nombre *</label>
                        <input type="text" x-model="supplierForm.nombre" class="compra-input" placeholder="Ej: Ferrum SRL">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div style="display: flex; flex-direction: column; gap: 4px;">
                        <label style="font-size: 0.7rem; font-weight: 800; color: #6B7280; text-transform: uppercase;">Teléfono *</label>
                        <input type="number" x-model.number="supplierForm.telefono" class="compra-input" placeholder="Ej: 71203040">
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 4px;">
                        <label style="font-size: 0.7rem; font-weight: 800; color: #6B7280; text-transform: uppercase;">Correo</label>
                        <input type="email" x-model="supplierForm.correo" class="compra-input" placeholder="Ej: ventas@ferrum.com">
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 0.7rem; font-weight: 800; color: #6B7280; text-transform: uppercase;">Dirección Física</label>
                    <input type="text" x-model="supplierForm.direccion" class="compra-input" placeholder="Ej: Av. Villazón Km 3">
                </div>

                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 0.7rem; font-weight: 800; color: #6B7280; text-transform: uppercase;">Descripción de Suministros *</label>
                    <textarea x-model="supplierForm.descripcion" class="compra-input" style="height: 70px; resize: vertical;" placeholder="Ej: Proveedor mayorista de clavos, pernos y alambres."></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #F3F4F6; padding-top: 16px; margin-top: 4px;">
                    <button type="button" @click="showAddSupplierModal = false" 
                            style="background: #F3F4F6; color: #4B5563; border: 1px solid #E5E7EB; font-weight: 700; padding: 10px 20px; border-radius: 10px; cursor: pointer; font-size: 0.9rem; transition: background 0.15s;"
                            onmouseover="this.style.background='#E5E7EB'" onmouseout="this.style.background='#F3F4F6'">
                        Cancelar
                    </button>
                    <button type="button" @click="saveSupplierOnTheFly()" 
                            style="background: linear-gradient(135deg, #6366F1, #4F46E5); color: white; border: none; font-weight: 700; padding: 10px 20px; border-radius: 10px; cursor: pointer; font-size: 0.9rem; box-shadow: 0 2px 8px rgba(99,102,241,0.25); transition: transform 0.15s;"
                            onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='none'">
                        Registrar y Seleccionar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════
         MODAL: REGISTRAR PRODUCTO AL VUELO
         ═══════════════════════════════════════════════════ -->
    <div x-show="showAddProductModal" 
         style="position: fixed; inset: 0; background: rgba(0,0,0,0.45); backdrop-filter: blur(4px); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 20px;"
         x-transition.opacity>
         
        <div @click.away="showAddProductModal = false" 
             style="background: white; border-radius: 16px; width: 100%; max-width: 500px; padding: 0; display: flex; flex-direction: column; box-shadow: 0 20px 40px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="transform scale-95 opacity-0"
             x-transition:enter-end="transform scale-100 opacity-100">
             
            <!-- Modal Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #F3F4F6; background: linear-gradient(135deg, #FFFBEB, #FEF3C7); border-radius: 16px 16px 0 0;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 32px; height: 32px; background: linear-gradient(135deg, #F59E0B, #D97706); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                    </div>
                    <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #1F2937;">Nuevo Producto</h3>
                </div>
                <button @click="showAddProductModal = false" style="background: none; border: none; cursor: pointer; color: #9CA3AF; padding: 4px; border-radius: 6px; transition: color 0.15s;" onmouseover="this.style.color='#4B5563'" onmouseout="this.style.color='#9CA3AF'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <!-- Formulario AJAX -->
            <div style="padding: 24px; display: flex; flex-direction: column; gap: 14px;">
                
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 0.7rem; font-weight: 800; color: #6B7280; text-transform: uppercase;">Nombre del Producto *</label>
                    <input type="text" x-model="productForm.nombre" class="compra-input" placeholder="Ej: Rotomartillo Inalámbrico 20V">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div style="display: flex; flex-direction: column; gap: 4px;">
                        <label style="font-size: 0.7rem; font-weight: 800; color: #6B7280; text-transform: uppercase;">Marca *</label>
                        <select x-model.number="productForm.id_marca" class="compra-input" style="cursor: pointer;">
                            <option value="">Selecciona...</option>
                            @foreach($marcas as $m)
                                <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 4px;">
                        <label style="font-size: 0.7rem; font-weight: 800; color: #6B7280; text-transform: uppercase;">Categoría *</label>
                        <select x-model.number="productForm.id_categoria" class="compra-input" style="cursor: pointer;">
                            <option value="">Selecciona...</option>
                            @foreach($categorias as $c)
                                <option value="{{ $c->idcategoria }}">{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 0.7rem; font-weight: 800; color: #6B7280; text-transform: uppercase;">Precio de Venta Sugerido (Bs.) *</label>
                    <input type="number" step="0.01" x-model.number="productForm.precio" class="compra-input" placeholder="Ej: 450.00">
                    <span style="font-size: 0.72rem; color: #9CA3AF;">El costo de compra se asigna en la lista de artículos.</span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 0.7rem; font-weight: 800; color: #6B7280; text-transform: uppercase;">Descripción</label>
                    <textarea x-model="productForm.descripcion" class="compra-input" style="height: 70px; resize: vertical;" placeholder="Ej: Rotomartillo con baterías recargables..."></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #F3F4F6; padding-top: 16px; margin-top: 4px;">
                    <button type="button" @click="showAddProductModal = false" 
                            style="background: #F3F4F6; color: #4B5563; border: 1px solid #E5E7EB; font-weight: 700; padding: 10px 20px; border-radius: 10px; cursor: pointer; font-size: 0.9rem; transition: background 0.15s;"
                            onmouseover="this.style.background='#E5E7EB'" onmouseout="this.style.background='#F3F4F6'">
                        Cancelar
                    </button>
                    <button type="button" @click="saveProductOnTheFly()" 
                            style="background: linear-gradient(135deg, #F59E0B, #D97706); color: white; border: none; font-weight: 700; padding: 10px 20px; border-radius: 10px; cursor: pointer; font-size: 0.9rem; box-shadow: 0 2px 8px rgba(245,158,11,0.25); transition: transform 0.15s;"
                            onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='none'">
                        Crear y Agregar al Carrito
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ESTILOS CSS PARA ESTA VISTA -->
<style>
    /* Input específico para formulario de compras (no usa .search-input del topbar) */
    .compra-input {
        width: 100%;
        padding: 10px 14px;
        border-radius: 10px;
        border: 1.5px solid #E5E7EB;
        font-size: 0.88rem;
        font-family: inherit;
        background: #FFFFFF;
        color: #1F2937;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
    }
    .compra-input:focus {
        border-color: #00AF9A;
        box-shadow: 0 0 0 3px rgba(0, 175, 154, 0.1);
    }
    .compra-input::placeholder {
        color: #C4C9D2;
    }
    .compra-input:disabled {
        background: #F9FAFB;
        color: #9CA3AF;
        cursor: not-allowed;
    }

    /* Grid responsive */
    .compra-grid {
        grid-template-columns: 1fr 420px !important;
    }
    @media (max-width: 960px) {
        .compra-grid {
            grid-template-columns: 1fr !important;
        }
    }

    /* Spinner animation */
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>

<!-- LÓGICA ALPINEJS -->
<script>
    function compraApp() {
        return {
            proveedores: @json($proveedores),
            productos: @json($productos),
            
            // Carrito
            cart: [],
            
            // Proveedor seleccionado y búsqueda predictiva
            supplierSearch: '',
            supplierDropdown: false,
            filteredSuppliersList: [],
            selectedSupplier: null,
            
            // Catálogo de búsqueda predictiva
            productSearch: '',
            productDropdown: false,
            filteredProductsList: [],
            
            // Loading
            loading: false,
            
            // Toasts de Control
            toast: {
                show: false,
                message: '',
                type: 'success'
            },
            
            // Formularios Modales al Vuelo
            showAddSupplierModal: false,
            supplierForm: {
                ci: '',
                nombre: '',
                telefono: '',
                descripcion: '',
                correo: '',
                direccion: ''
            },
            
            showAddProductModal: false,
            productForm: {
                nombre: '',
                precio: '',
                id_marca: '',
                id_categoria: '',
                descripcion: ''
            },
            
            init() {
                this.filterSuppliers();
                this.filterProducts();
                
                // Precargar artículos si provienen de un pedido de reabastecimiento
                @if(isset($preloadedItems) && count($preloadedItems) > 0)
                    const preloaded = @json($preloadedItems);
                    preloaded.forEach(item => {
                        this.cart.push({
                            id: item.idproducto,
                            nombre: item.nombre,
                            stock_actual: item.stock_actual,
                            cantidad: item.cantidad,
                            precio_unitario: item.precio_unitario
                        });
                    });
                    this.showToast('Artículos del pedido cargados.');
                @endif
            },
            
            // Filtro de proveedores interactivo
            filterSuppliers() {
                if (this.supplierSearch.trim() === '') {
                    this.filteredSuppliersList = this.proveedores;
                    return;
                }
                const q = this.supplierSearch.toLowerCase();
                this.filteredSuppliersList = this.proveedores.filter(s => 
                    s.nombre.toLowerCase().includes(q) || 
                    s.ci.toString().includes(q)
                );
            },
            
            // Seleccionar proveedor predictivo
            selectSupplier(supplier) {
                this.selectedSupplier = supplier;
                this.supplierSearch = supplier.nombre;
                this.supplierDropdown = false;
                this.showToast('Proveedor asignado.');
            },
            
            // Filtro predictivo de productos de catálogo
            filterProducts() {
                if (this.productSearch.trim() === '') {
                    this.filteredProductsList = this.productos;
                    return;
                }
                const q = this.productSearch.toLowerCase();
                this.filteredProductsList = this.productos.filter(p => 
                    p.nombre.toLowerCase().includes(q) || 
                    p.idproducto.toString().includes(q)
                );
            },
            
            // Agregar artículo seleccionado al carro de compra
            addProductToCart(prod) {
                // Verificar si ya está en la lista de compras
                const index = this.cart.findIndex(i => i.id === prod.idproducto);
                if (index !== -1) {
                    this.cart[index].cantidad += 1;
                    this.showToast('Artículo incrementado.');
                } else {
                    this.cart.push({
                        id: prod.idproducto,
                        nombre: prod.nombre,
                        stock_actual: prod.cantidad,
                        cantidad: 1,
                        precio_unitario: 0.00
                    });
                    this.showToast('Artículo agregado.');
                }
                
                this.productSearch = '';
                this.productDropdown = false;
                this.filterProducts();
            },
            
            // Validaciones numéricas de cantidad y costo
            validateItem(index) {
                const item = this.cart[index];
                if (isNaN(item.cantidad) || item.cantidad < 1) {
                    this.cart[index].cantidad = 1;
                }
                if (isNaN(item.precio_unitario) || item.precio_unitario < 0) {
                    this.cart[index].precio_unitario = 0;
                }
            },
            
            // Quitar artículo de la compra
            removeItem(index) {
                this.cart.splice(index, 1);
                this.showToast('Artículo removido.', 'error');
            },
            
            // Calcular Total acumulado en tiempo real
            calculateTotal() {
                let total = 0;
                this.cart.forEach(item => {
                    total += item.cantidad * item.precio_unitario;
                });
                return total.toFixed(2);
            },
            
            // Al Vuelo: Registrar y Seleccionar Proveedor nuevo
            saveSupplierOnTheFly() {
                const f = this.supplierForm;
                if (!f.ci || !f.nombre || !f.telefono || !f.descripcion) {
                    this.showToast('CI, Nombre, Teléfono y Descripción son campos obligatorios.', 'error');
                    return;
                }
                
                fetch('{{ route("admin.proveedores.rapido") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(f)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.proveedores.push(data.proveedor);
                        this.selectSupplier(data.proveedor);
                        this.showAddSupplierModal = false;
                        this.supplierForm = { ci: '', nombre: '', telefono: '', descripcion: '', correo: '', direccion: '' };
                        this.showToast('¡Proveedor registrado y seleccionado!');
                    } else {
                        this.showToast(data.message || 'Error al registrar al proveedor.', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    this.showToast('Error de servidor al registrar al proveedor.', 'error');
                });
            },
            
            // Al Vuelo: Registrar y Agregar Producto nuevo al carro
            saveProductOnTheFly() {
                const f = this.productForm;
                if (!f.nombre || !f.id_marca || !f.id_categoria || isNaN(f.precio) || f.precio === '') {
                    this.showToast('Nombre, Marca, Categoría y Precio son campos obligatorios.', 'error');
                    return;
                }
                
                fetch('{{ route("admin.productos.rapido") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(f)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.productos.push(data.producto);
                        this.addProductToCart(data.producto);
                        this.showAddProductModal = false;
                        this.productForm = { nombre: '', precio: '', id_marca: '', id_categoria: '', descripcion: '' };
                        this.showToast('¡Producto creado e incorporado a la compra!');
                    } else {
                        this.showToast(data.message || 'Error al registrar el producto.', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    this.showToast('Error de servidor al registrar el producto.', 'error');
                });
            },
            
            // Procesar y Guardar la Nota de Compra
            processPurchase() {
                if (this.cart.length === 0 || !this.selectedSupplier) return;
                
                this.loading = true;

                const urlParams = new URLSearchParams(window.location.search);
                const pedidoId = urlParams.get('pedido_id');
                
                const payload = {
                    ci_proveedor: this.selectedSupplier.ci,
                    productos: this.cart
                };

                if (pedidoId) {
                    payload.pedido_id = pedidoId;
                }
                
                fetch('{{ route("admin.compras.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => {
                    this.loading = false;
                    if (data.success) {
                        this.showToast('¡Compra procesada de forma exitosa!');
                        setTimeout(() => {
                            window.location.href = '{{ route("admin.compras.index") }}';
                        }, 1200);
                    } else {
                        this.showToast(data.message || 'Error al procesar la compra.', 'error');
                    }
                })
                .catch(err => {
                    this.loading = false;
                    console.error(err);
                    this.showToast('Error de servidor al procesar la compra.', 'error');
                });
            },
            
            // Helper Toast Notification
            showToast(msg, type = 'success') {
                this.toast.message = msg;
                this.toast.type = type;
                this.toast.show = true;
                setTimeout(() => {
                    this.toast.show = false;
                }, 2500);
            }
        }
    }
</script>
@endsection
