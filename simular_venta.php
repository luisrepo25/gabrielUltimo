<?php
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\Producto;

$p = Producto::find(101);
$p->cantidad = 5; // Stock is only 5
$p->save();

$nro = 9999;
DB::table('NotaVenta')->insert([
    'nro' => $nro,
    'fecha' => now()->toDateString(),
    'total' => 3300,
    'ci_cliente' => 1004,
    'ci_empleado' => 1002,
    'id_pago' => 1
]);

DB::table('detalleNotaVenta')->insert([
    'id_producto' => 101,
    'nro_factura' => $nro,
    'cantidad' => 60, // Sells 60 in the last 30 days -> 2 per day. Depletes 5 stock in 2.5 days.
    'subtotal' => 3300
]);

echo "SIMULACION COMPLETADA\n";
