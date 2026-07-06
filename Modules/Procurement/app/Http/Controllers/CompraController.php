<?php

namespace Modules\Procurement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Procurement\Models\Proveedor;
use Modules\Procurement\Models\NotaCompra;
use Modules\Procurement\Models\DetalleNotaCompra;
use Modules\Inventory\Models\Producto;
use Modules\Inventory\Models\Marca;
use Modules\Inventory\Models\Categoria;
use Modules\Audit\Models\Bitacora;
use Modules\Procurement\Models\PedidoReabastecimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CompraController extends Controller
{
    /**
     * Listado histórico de notas de compra hechas a proveedores
     */
    public function index()
    {
        $compras = NotaCompra::with(['proveedor', 'metodoPago', 'detalles.producto'])
            ->orderBy('fecha', 'desc')
            ->paginate(15);

        return view('admin.compras.index', compact('compras'));
    }

    public function create(Request $request)
    {
        $proveedores = Proveedor::orderBy('nombre', 'asc')->get();
        
        // Cargar productos activos que no estén eliminados lógicamente (SoftDeletes)
        $productos = Producto::orderBy('nombre', 'asc')->get();
        
        $marcas = Marca::orderBy('nombre', 'asc')->get();
        $categorias = Categoria::orderBy('nombre', 'asc')->get();

        $preloadedItems = [];
        if ($request->has('pedido_id')) {
            $pedido = PedidoReabastecimiento::with('detalles.producto')->find($request->pedido_id);
            if ($pedido) {
                foreach ($pedido->detalles as $detalle) {
                    if ($detalle->producto) {
                        $preloadedItems[] = [
                            'idproducto' => $detalle->idproducto,
                            'nombre' => $detalle->producto->nombre,
                            'stock_actual' => $detalle->producto->cantidad,
                            'cantidad' => $detalle->cantidad_sugerida,
                            'precio_unitario' => (float)($detalle->producto->costo ?? 0.00),
                        ];
                    }
                }
            }
        }

        return view('admin.compras.create', compact('proveedores', 'productos', 'marcas', 'categorias', 'preloadedItems'));
    }

    /**
     * Buscar un proveedor por CI de manera predictiva
     */
    public function buscarProveedor($ci)
    {
        $proveedor = Proveedor::where('ci', $ci)->first();

        if ($proveedor) {
            return response()->json([
                'success' => true,
                'proveedor' => $proveedor
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Proveedor no registrado.'
        ], 404);
    }

    /**
     * Registro rápido de un proveedor nuevo al vuelo (on-the-fly) desde compras
     */
    public function registrarProveedorRapido(Request $request)
    {
        $request->validate([
            'ci' => 'required|integer|unique:proveedor,ci',
            'nombre' => 'required|string|max:100',
            'telefono' => 'required|integer',
            'descripcion' => 'required|string',
            'correo' => 'nullable|email|max:50',
            'direccion' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            $proveedor = Proveedor::create([
                'ci' => $request->ci,
                'nombre' => $request->nombre,
                'telefono' => $request->telefono,
                'descripcion' => $request->descripcion,
                'correo' => $request->correo,
                'direccion' => $request->direccion
            ]);

            // Auditoría
            Bitacora::registrar('INSERTAR', 'proveedor', $request->ci, "Registro rápido de proveedor al vuelo desde Compras: {$request->nombre}");

            DB::commit();

            return response()->json([
                'success' => true,
                'proveedor' => $proveedor
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar al proveedor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registro rápido de un producto nuevo al vuelo (on-the-fly) desde compras
     */
    public function registrarProductoRapido(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'id_marca' => 'required|integer|exists:marca,id',
            'id_categoria' => 'required|integer|exists:categoria,idcategoria',
            'descripcion' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Generar el idproducto autocalculado de forma segura
            $ultimoId = Producto::max('idproducto') ?? 0;
            $nuevoId = $ultimoId + 1;

            $producto = Producto::create([
                'idproducto' => $nuevoId,
                'nombre' => $request->nombre,
                'precio' => $request->precio,
                'cantidad' => 0, // Inicialmente en 0; la compra le aumentará el stock
                'id_marca' => $request->id_marca,
                'id_categoria' => $request->id_categoria,
                'descripcion' => $request->descripcion ?? '',
            ]);

            // Auditoría
            Bitacora::registrar('INSERTAR', 'producto', $nuevoId, "Creación rápida de producto nuevo al vuelo desde Compras: {$request->nombre}");

            DB::commit();

            // Cargar relaciones para devolver el objeto completo
            $producto = Producto::with(['marca', 'categoria'])->where('idproducto', $nuevoId)->first();

            return response()->json([
                'success' => true,
                'producto' => $producto
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guardar la Nota de Compra y actualizar el stock
     */
    public function store(Request $request)
    {
        $request->validate([
            'ci_proveedor' => 'required|exists:proveedor,ci',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:producto,idproducto',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio_unitario' => 'required|numeric|min:0'
        ], [
            'productos.required' => 'Debes agregar al menos un artículo para registrar la compra.',
            'ci_proveedor.exists' => 'El proveedor seleccionado es inválido.',
        ]);

        DB::beginTransaction();
        try {
            // 1. Obtener correlativo de nota de compra
            $ultimoNro = NotaCompra::max('nro') ?? 0;
            $nroCompra = $ultimoNro + 1;

            $totalCompra = 0;
            $detallesCompra = [];

            // 2. Procesar productos y acumular totales
            foreach ($request->productos as $item) {
                $producto = Producto::where('idproducto', $item['id'])->lockForUpdate()->first();

                if (!$producto) {
                    throw new \Exception("El producto con ID {$item['id']} no existe en el catálogo.");
                }

                $cantidad = (int)$item['cantidad'];
                $costoUnitario = (float)$item['precio_unitario'];
                $subtotal = $cantidad * $costoUnitario;

                $totalCompra += $subtotal;

                // Aumentar automáticamente el stock del producto
                $producto->cantidad += $cantidad;
                $producto->save();

                // Almacenar el detalle
                $detallesCompra[] = [
                    'nro_factura' => $nroCompra,
                    'id_producto' => $producto->idproducto,
                    'precio_unitario' => $costoUnitario,
                    'cantidad' => $cantidad
                ];
            }

            // 3. Crear la Nota de Compra
            // ID de Pago predefinido estrictamente en 1 (Efectivo) por solicitud del usuario
            $compra = NotaCompra::create([
                'nro' => $nroCompra,
                'fecha' => now(),
                'total' => $totalCompra,
                'ci_proveedor' => $request->ci_proveedor,
                'id_pago' => 1 // EFECTIVO
            ]);

            // 4. Registrar los detalles de la compra
            foreach ($detallesCompra as $detalle) {
                DetalleNotaCompra::create($detalle);
            }

            // 5. Registro en bitácora de auditoría
            Bitacora::registrar('INSERTAR', 'notacompra', $nroCompra, "Reabastecimiento de inventario registrado con éxito por total de " . number_format($totalCompra, 2) . " BOB. Proveedor CI: {$request->ci_proveedor}");

            // 6. Si proviene de un pedido de reabastecimiento, marcarlo como atendido
            if ($request->has('pedido_id') && !empty($request->pedido_id)) {
                $pedido = PedidoReabastecimiento::find($request->pedido_id);
                if ($pedido) {
                    $pedido->estado = 'Atendido';
                    $pedido->save();
                    Bitacora::registrar('ACTUALIZAR', 'pedidos_reabastecimiento', $pedido->id, "Pedido de reabastecimiento #{$pedido->id} marcado como Atendido automáticamente al procesar la compra.");
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Compra procesada y stock actualizado con éxito.',
                'nro_compra' => $nroCompra
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la compra: ' . $e->getMessage()
            ], 500);
        }
    }
}
