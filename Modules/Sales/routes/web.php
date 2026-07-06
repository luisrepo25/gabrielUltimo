<?php

use Illuminate\Support\Facades\Route;
use Modules\Sales\Http\Controllers\CartController;
use Modules\Sales\Http\Controllers\VentaController;
use Modules\Sales\Http\Controllers\CajaController;
use Modules\Sales\Http\Controllers\PromocionController;

// ─────────────────────────────────────────────────────────
// PÚBLICAS — accesibles sin login
// ─────────────────────────────────────────────────────────
Route::get('/carrito', [CartController::class, 'index'])->name('carrito.index');
Route::post('/carrito/add', [CartController::class, 'add'])->name('carrito.add');
Route::post('/carrito/update', [CartController::class, 'update'])->name('carrito.update');
Route::get('/carrito/cotizacion', [CartController::class, 'generarCotizacion'])->name('cotizacion.generar');
Route::post('/carrito/remove', [CartController::class, 'remove'])->name('carrito.remove');
Route::post('/carrito/clear', [CartController::class, 'clear'])->name('carrito.clear');

Route::get('/ofertas', [PromocionController::class, 'indexPublic'])->name('promociones.public');
Route::post('/promociones/{id}/comprar', [CartController::class, 'addPromocion'])->name('promocion.comprar');

// ─────────────────────────────────────────────────────────
// AUTENTICADAS — requieren login
// ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    Route::post('/carrito/checkout', [CartController::class, 'checkout'])->name('carrito.checkout');

    // Cotizaciones persistentes
    Route::post('/cotizaciones/guardar', [CartController::class, 'guardarCotizacion'])->name('cotizaciones.guardar');
    Route::get('/cotizaciones/guardadas', [CartController::class, 'verCotizacionesGuardadas'])->name('cotizaciones.guardadas');
    Route::post('/cotizaciones/cargar/{id}', [CartController::class, 'cargarCotizacion'])->name('cotizaciones.cargar');
    Route::delete('/cotizaciones/{id}', [CartController::class, 'eliminarCotizacion'])->name('cotizaciones.eliminar');

    Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
    Route::get('/ventas/crear', [VentaController::class, 'create'])->name('ventas.create');
    Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
    Route::get('/api/clientes/buscar/{ci}', [VentaController::class, 'buscarCliente']);
    Route::post('/api/clientes/rapido', [VentaController::class, 'registrarCliente'])->name('clientes.rapido');
    Route::get('/ventas/comprobante/{nro}', [VentaController::class, 'imprimir'])->name('ventas.comprobante');

    Route::middleware('admin')->group(function () {
        Route::get('/caja', [CajaController::class, 'index'])->name('caja.index');
        Route::post('/caja/apertura', [CajaController::class, 'apertura'])->name('caja.apertura');
        Route::post('/caja/corte/{caja}', [CajaController::class, 'corte'])->name('caja.corte');
        Route::get('/caja/reporte/{caja}', [CajaController::class, 'reporte'])->name('caja.reporte');

        Route::get('/promociones', [PromocionController::class, 'index'])->name('admin.promociones.index');
        Route::get('/promociones/crear', [PromocionController::class, 'create'])->name('admin.promociones.create');
        Route::post('/promociones', [PromocionController::class, 'store'])->name('admin.promociones.store');
        Route::get('/promociones/{id}/editar', [PromocionController::class, 'edit'])->name('admin.promociones.edit');
        Route::put('/promociones/{id}', [PromocionController::class, 'update'])->name('admin.promociones.update');
        Route::delete('/promociones/{id}', [PromocionController::class, 'destroy'])->name('admin.promociones.destroy');
    });
});
