<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production') || env('VERCEL')) {
            URL::forceScheme('https');
        }

        // 1. Rol ADMINISTRADOR (Control Total)
        \Illuminate\Support\Facades\Gate::define('admin', function ($user) {
            // Caso 1: Es empleado y tiene el rol de Administrador asignado
            if ($user->tipoPersona === 'E') {
                return $user->empleado?->asignaciones()
                    ->whereHas('rol', fn($q) => $q->where('nombre', 'Administrador'))
                    ->where('estado', 'Activo')
                    ->exists();
            }
            return false;
        });

        // 2. Rol ALMACENERO (Gestión de Stock/Productos)
        \Illuminate\Support\Facades\Gate::define('almacenero', function ($user) {
            if ($user->tipoPersona === 'E') {
                return $user->empleado?->asignaciones()
                    ->whereHas('rol', fn($q) => $q->where('nombre', 'Almacenero'))
                    ->where('estado', 'Activo')
                    ->exists();
            }
            return false;
        });

        // 3. Rol CLIENTE (Solo Consulta)
        \Illuminate\Support\Facades\Gate::define('cliente', function ($user) {
            return $user->tipoPersona === 'C' || strtolower($user->tipoPersona) === 'cliente';
        });
    }
}
