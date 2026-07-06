<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Inventory\Models\Producto;
use Modules\Audit\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InventarioController extends Controller
{
    /**
     * Mostrar el panel principal de Gestión de Inventario
     */
    public function index()
    {
        // 1. Obtener todos los productos con su categoría y marca
        $productos = Producto::with(['categoria', 'marca'])->orderBy('cantidad', 'asc')->get();

        // 2. Calcular KPIs (Métricas clave)
        $totalProductos = $productos->count();
        
        $valorTotalInventario = 0;
        $productosCriticos = 0;
        
        foreach ($productos as $prod) {
            // Patrimonio = precio/costo * cantidad (Usaremos costo si existe, sino precio)
            $costoBase = isset($prod->costo) ? $prod->costo : $prod->precio;
            $valorTotalInventario += ($costoBase * $prod->cantidad);
            
            // Stock crítico <= 5 (Límite definido por el usuario)
            if ($prod->cantidad <= 5) {
                $productosCriticos++;
            }
        }

        return view('admin.inventario.index', compact(
            'productos',
            'totalProductos',
            'valorTotalInventario',
            'productosCriticos'
        ));
    }

    /**
     * Ajustar el stock de un producto manualmente con justificación
     */
    public function ajustarStock(Request $request)
    {
        $request->validate([
            'idproducto' => 'required|exists:producto,idproducto',
            'nueva_cantidad' => 'required|integer|min:0',
            'motivo' => 'required|string|min:5|max:255'
        ]);

        DB::beginTransaction();
        try {
            $producto = Producto::where('idproducto', $request->idproducto)->lockForUpdate()->first();
            
            $cantidadAnterior = $producto->cantidad;
            $nuevaCantidad = (int) $request->nueva_cantidad;
            $diferencia = $nuevaCantidad - $cantidadAnterior;
            
            if ($diferencia === 0) {
                return back()->with('error_acceso', 'La nueva cantidad debe ser diferente a la actual.');
            }

            // Actualizar stock
            $producto->cantidad = $nuevaCantidad;
            $producto->save();

            // Registrar en Bitácora el movimiento de ajuste
            $tipoAjuste = $diferencia > 0 ? 'AUMENTO' : 'REDUCCIÓN';
            $detalle = "AJUSTE DE INVENTARIO ($tipoAjuste): Producto '{$producto->nombre}'. Stock anterior: {$cantidadAnterior}. Nuevo stock: {$nuevaCantidad}. Motivo: {$request->motivo}";
            
            Bitacora::registrar('ACTUALIZAR', 'producto', $producto->idproducto, $detalle);

            DB::commit();

            return back()->with('success', 'El inventario del producto ha sido ajustado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error_acceso', 'Error al ajustar el stock: ' . $e->getMessage());
        }
    }
}
