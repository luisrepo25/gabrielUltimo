<?php

namespace Modules\Procurement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Procurement\Models\PedidoReabastecimiento;
use Modules\Procurement\Models\PedidoReabastecimientoDetalle;
use Modules\Inventory\Models\Producto;
use Modules\Audit\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PedidoReabastecimientoController extends Controller
{
    /**
     * Listado de pedidos de reabastecimiento
     */
    public function index()
    {
        $pedidos = PedidoReabastecimiento::with(['empleado', 'detalles.producto'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.pedidos.index', compact('pedidos'));
    }

    /**
     * Formulario para crear nuevo pedido de reabastecimiento
     */
    public function create()
    {
        $productos = Producto::orderBy('nombre', 'asc')->get();
        // Productos con stock bajo (menos de 5 unidades)
        $stockBajo = Producto::where('cantidad', '<', 5)->orderBy('cantidad', 'asc')->get();

        return view('admin.pedidos.create', compact('productos', 'stockBajo'));
    }

    /**
     * Guardar el pedido de reabastecimiento
     */
    public function store(Request $request)
    {
        $request->validate([
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:producto,idproducto',
            'productos.*.cantidad' => 'required|integer|min:1',
        ], [
            'productos.required' => 'Debes agregar al menos un producto al pedido.',
        ]);

        DB::beginTransaction();
        try {
            $pedido = PedidoReabastecimiento::create([
                'ci_empleado' => Auth::user()->ci,
                'fecha' => now(),
                'estado' => 'Pendiente',
                'observaciones' => $request->observaciones,
            ]);

            foreach ($request->productos as $item) {
                PedidoReabastecimientoDetalle::create([
                    'pedido_id' => $pedido->id,
                    'idproducto' => $item['id'],
                    'cantidad_sugerida' => $item['cantidad'],
                ]);
            }

            Bitacora::registrar('INSERTAR', 'pedidos_reabastecimiento', $pedido->id, "Pedido de reabastecimiento creado con " . count($request->productos) . " productos por empleado CI: " . Auth::user()->ci);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pedido de reabastecimiento registrado exitosamente.',
                'id' => $pedido->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ver detalle de un pedido
     */
    public function show($id)
    {
        $pedido = PedidoReabastecimiento::with(['empleado', 'detalles.producto'])
            ->findOrFail($id);

        return view('admin.pedidos.show', compact('pedido'));
    }

    /**
     * Marcar pedido como atendido
     */
    public function atender($id)
    {
        $pedido = PedidoReabastecimiento::findOrFail($id);

        if ($pedido->estado !== 'Pendiente') {
            return back()->with('error', 'Este pedido ya fue procesado.');
        }

        $pedido->estado = 'Atendido';
        $pedido->save();

        Bitacora::registrar('ACTUALIZAR', 'pedidos_reabastecimiento', $pedido->id, "Pedido de reabastecimiento Nro. {$pedido->id} marcado como ATENDIDO.");

        return back()->with('success', 'Pedido marcado como atendido. Puedes proceder a registrar la compra correspondiente.');
    }

    /**
     * Cancelar un pedido
     */
    public function cancelar($id)
    {
        $pedido = PedidoReabastecimiento::findOrFail($id);

        if ($pedido->estado !== 'Pendiente') {
            return back()->with('error', 'Este pedido ya fue procesado.');
        }

        $pedido->estado = 'Cancelado';
        $pedido->save();

        Bitacora::registrar('ACTUALIZAR', 'pedidos_reabastecimiento', $pedido->id, "Pedido de reabastecimiento Nro. {$pedido->id} CANCELADO.");

        return back()->with('success', 'Pedido cancelado correctamente.');
    }
}
