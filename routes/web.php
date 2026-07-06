<?php

use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────
// PÚBLICAS — accesibles sin login
// ─────────────────────────────────────────────────────────

// ─────────────────────────────────────────────────────────
// AUTENTICADAS — requieren login
// ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard personal (Breeze)
    Route::get('/dashboard', function () {
        $isAdmin = \Illuminate\Support\Facades\Gate::allows('admin');

        $totalProductos = null;
        $totalUsuarios  = null;
        $predicciones   = collect();
        $ultimasBitacoras = collect();
        $finanzas = null;

        if ($isAdmin) {
            $totalProductos   = \Modules\Inventory\Models\Producto::count();
            $totalUsuarios    = \Modules\Access\Models\Usuario::count();
            
            // Algoritmo Predictivo de Inventario
            $sql = "
                SELECT 
                    p.idproducto,
                    p.nombre,
                    p.cantidad AS stock_actual,
                    COALESCE(SUM(dnv.cantidad), 0) AS total_vendido_30d,
                    (COALESCE(SUM(dnv.cantidad), 0) / 30.0) AS velocidad_diaria,
                    (p.cantidad / (COALESCE(SUM(dnv.cantidad), 0) / 30.0)) AS dias_restantes
                FROM producto p
                LEFT JOIN detalleNotaVenta dnv ON p.idproducto = dnv.id_producto
                LEFT JOIN NotaVenta nv ON dnv.nro_factura = nv.nro AND nv.fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                WHERE p.deleted_at IS NULL
                GROUP BY p.idproducto, p.nombre, p.cantidad
                HAVING velocidad_diaria > 0 AND dias_restantes < 7
                ORDER BY dias_restantes ASC
                LIMIT 10
            ";
            $predicciones = collect(\Illuminate\Support\Facades\DB::select($sql));
            
            $ultimasBitacoras = \Modules\Audit\Models\Bitacora::orderBy('created_at', 'desc')->take(5)->get();

            // Lógica de Consumo de API (Servicio de Divisas)
            $totalIngresosBob = \Illuminate\Support\Facades\DB::table('NotaVenta')->sum('total');
            $currencyService = new \App\Services\CurrencyService();
            $finanzas = $currencyService->convertFromBob($totalIngresosBob);
        }

        return view('dashboard.index', compact('isAdmin', 'totalProductos', 'totalUsuarios', 'predicciones', 'ultimasBitacoras', 'finanzas'));
    })->name('dashboard');

    // Generar Pedido Predictivo
    Route::post('/dashboard/generar-pedido/{idproducto}', function (\Illuminate\Http\Request $request, $idproducto) {
        $producto = \Modules\Inventory\Models\Producto::findOrFail($idproducto);
        $velocidad = $request->input('velocidad', 1);
        $cantidadSugerida = ceil($velocidad * 30); // Cubrir 1 mes
        
        $pedidoId = \Illuminate\Support\Facades\DB::table('pedidos_reabastecimiento')->insertGetId([
            'ci_empleado' => auth()->user()->ci,
            'fecha' => now()->toDateString(),
            'estado' => 'Pendiente',
            'observaciones' => 'Pedido generado por IA Predictiva. Velocidad: ' . round($velocidad, 2) . ' unid/día'
        ]);
        
        \Illuminate\Support\Facades\DB::table('pedido_reabastecimiento_detalles')->insert([
            'pedido_id' => $pedidoId,
            'idproducto' => $idproducto,
            'cantidad_sugerida' => $cantidadSugerida
        ]);
        
        if (class_exists(\Modules\Audit\Models\Bitacora::class)) {
            \Modules\Audit\Models\Bitacora::registrar('INSERTAR', 'pedidos_reabastecimiento', $pedidoId, 'Pedido automático IA para ' . $producto->nombre);
        }
        
        return back()->with('success', 'Pedido sugerido generado por IA para 30 días (' . $cantidadSugerida . ' unidades).');
    })->name('dashboard.generar_pedido')->middleware('can:admin');
});