<?php

use Illuminate\Support\Facades\Route;
use Modules\Access\Http\Controllers\ProfileController;
use Modules\Access\Http\Controllers\UsuarioController;
use Modules\Access\Http\Controllers\TrabajoController;
use Modules\Access\Http\Controllers\Auth\AuthenticatedSessionController;
use Modules\Access\Http\Controllers\Auth\ConfirmablePasswordController;
use Modules\Access\Http\Controllers\Auth\EmailVerificationNotificationController;
use Modules\Access\Http\Controllers\Auth\EmailVerificationPromptController;
use Modules\Access\Http\Controllers\Auth\NewPasswordController;
use Modules\Access\Http\Controllers\Auth\PasswordController;
use Modules\Access\Http\Controllers\Auth\PasswordResetLinkController;
use Modules\Access\Http\Controllers\Auth\RegisteredUserController;
use Modules\Access\Http\Controllers\Auth\VerifyEmailController;

// ----------------- RUTAS DE INVITADOS (GUEST) -----------------
Route::middleware(['web', 'guest'])->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

// ----------------- RUTAS AUTENTICADAS (AUTH) -----------------
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Trabajos y asignaciones
    Route::get('/trabajos', [TrabajoController::class, 'index'])->name('trabajos.index');

    // Solo Administradores
    Route::middleware('admin')->group(function () {
        Route::get('/api/usuario/{ci}', [UsuarioController::class, 'getUsuario']);
        Route::resource('usuarios', UsuarioController::class);

        Route::post('/trabajos/rol', [TrabajoController::class, 'store'])->name('trabajos.store');
        Route::post('/trabajos/asignar', [TrabajoController::class, 'asignar'])->name('trabajos.asignar');
        Route::post('/trabajos/baja', [TrabajoController::class, 'darDeBaja'])->name('trabajos.baja');
    });
});
