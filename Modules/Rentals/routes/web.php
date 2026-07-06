<?php

use Illuminate\Support\Facades\Route;
use Modules\Rentals\Http\Controllers\AlquilerController;
use Modules\Rentals\Http\Controllers\MaquinariaController;
use Modules\Rentals\Http\Controllers\MantenimientoController;

// ─────────────────────────────────────────────────────────
// PÚBLICAS — accesibles sin login
// ─────────────────────────────────────────────────────────
Route::get('/maquinarias/catalogo', [MaquinariaController::class, 'indexPublic'])->name('maquinarias.catalogo');

// ─────────────────────────────────────────────────────────
// AUTENTICADAS — requieren login
// ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    Route::resource('maquinarias', MaquinariaController::class);
    Route::get('/alquileres', [AlquilerController::class, 'index'])->name('alquileres.index');
    Route::get('/alquileres/crear', [AlquilerController::class, 'create'])->name('alquileres.create');
    Route::post('/alquileres', [AlquilerController::class, 'store'])->name('alquileres.store');
    Route::get('/alquileres/{id}', [AlquilerController::class, 'show'])->name('alquileres.show');
    Route::post('/alquileres/{id}/devolucion', [AlquilerController::class, 'registrarDevolucion'])->name('alquileres.devolucion');
    Route::get('/alquileres/{id}/comprobante', [AlquilerController::class, 'imprimir'])->name('alquileres.comprobante');

    Route::middleware('admin')->group(function () {
        Route::get('/mantenimientos', [MantenimientoController::class, 'index'])->name('admin.mantenimientos.index');
        Route::get('/mantenimientos/crear', [MantenimientoController::class, 'create'])->name('admin.mantenimientos.create');
        Route::post('/mantenimientos', [MantenimientoController::class, 'store'])->name('admin.mantenimientos.store');
        Route::get('/mantenimientos/{id}/editar', [MantenimientoController::class, 'edit'])->name('admin.mantenimientos.edit');
        Route::put('/mantenimientos/{id}', [MantenimientoController::class, 'update'])->name('admin.mantenimientos.update');
    });
});
