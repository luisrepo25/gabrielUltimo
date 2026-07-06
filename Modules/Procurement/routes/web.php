<?php

use Illuminate\Support\Facades\Route;
use Modules\Procurement\Http\Controllers\ProveedorController;
use Modules\Procurement\Http\Controllers\CompraController;
use Modules\Procurement\Http\Controllers\DevolucionController;
use Modules\Procurement\Http\Controllers\PedidoReabastecimientoController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware('admin')->group(function () {

        // Gestión de Proveedores
        Route::resource('proveedores', ProveedorController::class)->names('admin.proveedores');

        // Gestión de Compras
        Route::get('/compras', [CompraController::class, 'index'])->name('admin.compras.index');
        Route::get('/compras/crear', [CompraController::class, 'create'])->name('admin.compras.create');
        Route::post('/compras', [CompraController::class, 'store'])->name('admin.compras.store');
        Route::get('/api/proveedores/buscar/{ci}', [CompraController::class, 'buscarProveedor']);
        Route::post('/api/proveedores/rapido', [CompraController::class, 'registrarProveedorRapido'])->name('admin.proveedores.rapido');
        Route::post('/api/productos/rapido', [CompraController::class, 'registrarProductoRapido'])->name('admin.productos.rapido');

        // CU16: Gestionar Devoluciones y Garantías
        Route::get('/devoluciones', [DevolucionController::class, 'index'])->name('admin.devoluciones.index');
        Route::get('/devoluciones/crear', [DevolucionController::class, 'create'])->name('admin.devoluciones.create');
        Route::post('/devoluciones', [DevolucionController::class, 'store'])->name('admin.devoluciones.store');
        Route::get('/devoluciones/{id}', [DevolucionController::class, 'show'])->name('admin.devoluciones.show');
        Route::post('/devoluciones/{id}/aprobar', [DevolucionController::class, 'aprobar'])->name('admin.devoluciones.aprobar');
        Route::post('/devoluciones/{id}/rechazar', [DevolucionController::class, 'rechazar'])->name('admin.devoluciones.rechazar');
        Route::get('/api/devoluciones/factura/{nro}', [DevolucionController::class, 'buscarFactura']);

        // CU19: Gestionar Pedidos de Reabastecimiento
        Route::get('/pedidos-reabastecimiento', [PedidoReabastecimientoController::class, 'index'])->name('admin.pedidos.index');
        Route::get('/pedidos-reabastecimiento/crear', [PedidoReabastecimientoController::class, 'create'])->name('admin.pedidos.create');
        Route::post('/pedidos-reabastecimiento', [PedidoReabastecimientoController::class, 'store'])->name('admin.pedidos.store');
        Route::get('/pedidos-reabastecimiento/{id}', [PedidoReabastecimientoController::class, 'show'])->name('admin.pedidos.show');
        Route::post('/pedidos-reabastecimiento/{id}/atender', [PedidoReabastecimientoController::class, 'atender'])->name('admin.pedidos.atender');
        Route::post('/pedidos-reabastecimiento/{id}/cancelar', [PedidoReabastecimientoController::class, 'cancelar'])->name('admin.pedidos.cancelar');
    });
});
