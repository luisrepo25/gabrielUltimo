<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\ProductoController;
use Modules\Inventory\Http\Controllers\CategoriaController;
use Modules\Inventory\Http\Controllers\MarcaController;
use Modules\Inventory\Http\Controllers\InventarioController;

// ----------------- RUTAS PÚBLICAS -----------------
Route::middleware(['web'])->group(function () {
    Route::get('/', [ProductoController::class, 'index'])->name('inventario');
    Route::get('/catalogo/producto/{id}', [ProductoController::class, 'showPublic'])->name('producto.show');

    // Marcas públicas
    Route::get('/marcas', [MarcaController::class, 'indexPublic'])->name('marcas.index');
    Route::get('/marcas/{id}/productos', [MarcaController::class, 'productosPorMarca'])->name('marcas.productos');

    // Categorías públicas
    Route::get('/categorias', [CategoriaController::class, 'indexPublic'])->name('categorias.index');
    Route::get('/categorias/{id}/productos', [CategoriaController::class, 'productosPorCategoria'])->name('categorias.productos');
});

// ----------------- RUTAS AUTENTICADAS -----------------
Route::middleware(['web', 'auth', 'verified'])->group(function () {
    Route::get('/api/producto/{id}', [ProductoController::class, 'getProducto']);
    Route::resource('productos', ProductoController::class);

    // Solo Administradores / Staff (según corresponda)
    Route::middleware('admin')->group(function () {
        // Admin Marcas
        Route::get('/admin/marcas', [MarcaController::class, 'index'])->name('admin.marcas.index');
        Route::get('/admin/marcas/crear', [MarcaController::class, 'create'])->name('admin.marcas.create');
        Route::post('/admin/marcas', [MarcaController::class, 'store'])->name('admin.marcas.store');
        Route::get('/admin/marcas/{id}/editar', [MarcaController::class, 'edit'])->name('admin.marcas.edit');
        Route::put('/admin/marcas/{id}', [MarcaController::class, 'update'])->name('admin.marcas.update');
        Route::delete('/admin/marcas/{id}', [MarcaController::class, 'destroy'])->name('admin.marcas.destroy');

        // Admin Categorías
        Route::get('/admin/categorias', [CategoriaController::class, 'index'])->name('admin.categorias.index');
        Route::get('/admin/categorias/crear', [CategoriaController::class, 'create'])->name('admin.categorias.create');
        Route::post('/admin/categorias', [CategoriaController::class, 'store'])->name('admin.categorias.store');
        Route::get('/admin/categorias/{id}/editar', [CategoriaController::class, 'edit'])->name('admin.categorias.edit');
        Route::put('/admin/categorias/{id}', [CategoriaController::class, 'update'])->name('admin.categorias.update');
        Route::delete('/admin/categorias/{id}', [CategoriaController::class, 'destroy'])->name('admin.categorias.destroy');

        // Gestión de Inventario (Ajustes de stock)
        Route::get('/inventario', [InventarioController::class, 'index'])->name('admin.inventario.index');
        Route::post('/inventario/ajustar', [InventarioController::class, 'ajustarStock'])->name('admin.inventario.ajustar');
    });
});
