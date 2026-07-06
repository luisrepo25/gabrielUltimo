<?php

use Illuminate\Support\Facades\Route;
use Modules\Audit\Http\Controllers\AuditController;
use Modules\Audit\Http\Controllers\IaReportController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware('admin')->group(function () {
        Route::get('/bitacora', [AuditController::class, 'index'])->name('bitacora.index');
    });

    // Reportes por IA - Accesible para todos
    Route::get('/reportes-ia', [IaReportController::class, 'index'])->name('reportes-ia.index');
    Route::post('/reportes-ia/consultar', [IaReportController::class, 'consultar'])->name('reportes-ia.consultar');
});
