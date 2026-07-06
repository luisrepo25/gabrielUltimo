<?php

namespace Modules\Procurement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Procurement\Models\Devolucion;
use Modules\Procurement\Models\DevolucionDetalle;
use Modules\Sales\Models\FacturaVenta;
use Modules\Inventory\Models\Producto;
use Modules\Audit\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DevolucionController extends Controller
{
    /**
     * Listado de devoluciones y garantías
     */
    public function index()
    {
        $devoluciones = Devolucion::with(['factura.cliente', 'empleado', 'detalles.producto'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.devoluciones.index', compact('devoluciones'));
    }

    /**
     * Formulario para registrar nueva devolución/garantía
     */
    public function create()
    {
        return view('admin.devoluciones.create');
    }

    /**
     * Buscar factura por número para asociar la devolución
     */
    public function buscarFactura($nro)
    {
        $factura = FacturaVenta::with(['cliente', 'detalles.producto'])
            ->where('nro', $nro)
            ->first();

        if ($factura) {
            return response()->json([
                'success' => true,
                'factura' => $factura
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Factura no encontrada.'
        ], 404);
    }

    /**
     * Guardar nueva devolución/garantía
     */
    public function store(Request $request)
    {
        $request->validate([
            'nro_factura' => 'required|exists:NotaVenta,nro',
            'tipo' => 'required|in:Devolución,Garantía',
            'motivo' => 'required|string|max:500',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:producto,idproducto',
            'productos.*.cantidad' => 'required|integer|min:1',
        ], [
            'productos.required' => 'Debes seleccionar al menos un producto para la devolución.',
            'nro_factura.exists' => 'La factura indicada no existe en el sistema.',
        ]);

        DB::beginTransaction();
        try {
            $devolucion = Devolucion::create([
                'nro_factura' => $request->nro_factura,
                'tipo' => $request->tipo,
                'motivo' => $request->motivo,
                'fecha' => now(),
                'estado' => 'Pendiente',
                'ci_empleado' => Auth::user()->ci,
                'observaciones' => $request->observaciones,
            ]);

            foreach ($request->productos as $item) {
                DevolucionDetalle::create([
                    'devolucion_id' => $devolucion->id,
                    'idproducto' => $item['id'],
                    'cantidad' => $item['cantidad'],
                ]);
            }

            Bitacora::registrar('INSERTAR', 'devoluciones', $devolucion->id, "Registro de {$request->tipo} para factura Nro. {$request->nro_factura}. Motivo: {$request->motivo}");

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => ucfirst($request->tipo) . ' registrada correctamente.',
                'id' => $devolucion->id,
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
     * Ver detalle de una devolución
     */
    public function show($id)
    {
        $devolucion = Devolucion::with(['factura.cliente', 'empleado', 'detalles.producto'])
            ->findOrFail($id);

        return view('admin.devoluciones.show', compact('devolucion'));
    }

    /**
     * Aprobar una devolución (devuelve stock al inventario y reembolsa el total de la factura)
     */
    public function aprobar($id)
    {
        DB::beginTransaction();
        try {
            $devolucion = Devolucion::with('detalles')->findOrFail($id);

            if ($devolucion->estado !== 'Pendiente') {
                return back()->with('error', 'Esta devolución ya fue procesada.');
            }

            // Devolver stock
            foreach ($devolucion->detalles as $detalle) {
                $producto = Producto::where('idproducto', $detalle->idproducto)->lockForUpdate()->first();
                if ($producto) {
                    $producto->cantidad += $detalle->cantidad;
                    $producto->save();
                }
            }

            // Si es de tipo "Devolución", descontamos del total de la factura y actualizamos las cantidades en el detalle
            if ($devolucion->tipo === 'Devolución') {
                $factura = FacturaVenta::where('nro', $devolucion->nro_factura)->first();
                if ($factura) {
                    $totalDescuentoFactura = 0;

                    foreach ($devolucion->detalles as $detalle) {
                        // Buscar el detalle de la factura original
                        $detalleFactura = DB::table('detalleNotaVenta')
                            ->where('nro_factura', $devolucion->nro_factura)
                            ->where('id_producto', $detalle->idproducto)
                            ->first();

                        if ($detalleFactura) {
                            $cantidadOriginal = (int)$detalleFactura->cantidad;
                            $cantidadDevuelta = (int)$detalle->cantidad;

                            if ($cantidadOriginal > 0) {
                                $precioUnitario = (float)$detalleFactura->precio_unitario;
                                $descuentoOriginal = (float)$detalleFactura->descuento;
                                
                                // Precio neto unitario considerando el descuento original de la línea
                                $precioNetoUnitario = ($precioUnitario * $cantidadOriginal - $descuentoOriginal) / $cantidadOriginal;
                                
                                $montoADescontar = $precioNetoUnitario * $cantidadDevuelta;
                                $totalDescuentoFactura += $montoADescontar;

                                $nuevaCantidad = max(0, $cantidadOriginal - $cantidadDevuelta);

                                if ($nuevaCantidad <= 0) {
                                    // Si no quedan unidades, eliminamos el registro del detalle de la nota de venta
                                    DB::table('detalleNotaVenta')
                                        ->where('nro_factura', $devolucion->nro_factura)
                                        ->where('id_producto', $detalle->idproducto)
                                        ->delete();
                                } else {
                                    // Si quedan unidades, actualizamos la cantidad y el descuento de forma proporcional
                                    $nuevoDescuento = $descuentoOriginal - (($descuentoOriginal / $cantidadOriginal) * $cantidadDevuelta);
                                    DB::table('detalleNotaVenta')
                                        ->where('nro_factura', $devolucion->nro_factura)
                                        ->where('id_producto', $detalle->idproducto)
                                        ->update([
                                            'cantidad' => $nuevaCantidad,
                                            'descuento' => round($nuevoDescuento, 2)
                                        ]);
                                }
                            }
                        }
                    }

                    // Actualizar el total de la factura
                    $factura->total = max(0, $factura->total - $totalDescuentoFactura);
                    $factura->save();
                }
            }

            $devolucion->estado = 'Aprobado';
            $devolucion->save();

            Bitacora::registrar('ACTUALIZAR', 'devoluciones', $devolucion->id, "Devolución/Garantía Nro. {$devolucion->id} APROBADA. Stock y factura actualizados.");

            DB::commit();

            return back()->with('success', 'Devolución aprobada. El stock y el total de la factura se actualizaron con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al aprobar: ' . $e->getMessage());
        }
    }

    /**
     * Rechazar una devolución
     */
    public function rechazar($id)
    {
        $devolucion = Devolucion::findOrFail($id);

        if ($devolucion->estado !== 'Pendiente') {
            return back()->with('error', 'Esta devolución ya fue procesada.');
        }

        $devolucion->estado = 'Rechazado';
        $devolucion->save();

        Bitacora::registrar('ACTUALIZAR', 'devoluciones', $devolucion->id, "Devolución/Garantía Nro. {$devolucion->id} RECHAZADA.");

        return back()->with('success', 'Devolución rechazada correctamente.');
    }
}
