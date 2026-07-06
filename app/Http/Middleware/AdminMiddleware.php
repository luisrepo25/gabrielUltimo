<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Solo permite el paso a usuarios que cumplan la Gate 'admin'.
     * Redirige al inventario con un mensaje si no tienen permisos.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Gate::allows('admin')) {
            return redirect('/')
                ->with('error_acceso', 'No tienes permisos para acceder a esa sección.');
        }

        return $next($request);
    }
}
