<?php

namespace Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\Producto;
use Modules\Sales\Models\Carrito;
use Modules\Sales\Models\Promocion;
use Modules\Sales\Models\FacturaVenta;
use Modules\Sales\Models\DetalleFacturaVenta;
use Modules\Audit\Models\Bitacora;
use Modules\Access\Models\Cliente;
use Modules\Sales\Models\Cotizacion;
use Modules\Sales\Models\CotizacionDetalle;

class CartController extends Controller
{
    private function mergeSessionCart()
    {
        if (Auth::check() && session()->has('carrito')) {
            $sessionCart = session()->get('carrito');
            $ci = Auth::user()->ci;

            foreach ($sessionCart as $idproducto => $item) {
                $dbItem = Carrito::where('ci_usuario', $ci)->where('idproducto', $idproducto)->first();
                if ($dbItem) {
                    $dbItem->cantidad += $item['cantidad'];
                    $dbItem->save();
                } else {
                    Carrito::create([
                        'ci_usuario' => $ci,
                        'idproducto' => $idproducto,
                        'cantidad' => $item['cantidad']
                    ]);
                }
            }
            session()->forget('carrito');
        }
    }

    public function index()
    {
        $this->mergeSessionCart();

        $cartItems = [];
        $total = 0;

        if (Auth::check()) {
            $items = Carrito::with('producto')->where('ci_usuario', Auth::user()->ci)->get();
            foreach ($items as $item) {
                if ($item->producto) {
                    $subtotal = $item->producto->precio * $item->cantidad;
                    $cartItems[] = [
                        'idproducto' => $item->idproducto,
                        'nombre' => $item->producto->nombre,
                        'precio' => $item->producto->precio,
                        'cantidad' => $item->cantidad,
                        'subtotal' => $subtotal
                    ];
                    $total += $subtotal;
                }
            }
        } else {
            $sessionCart = session()->get('carrito', []);
            foreach ($sessionCart as $idproducto => $item) {
                $producto = Producto::find($idproducto);
                if ($producto) {
                    $subtotal = $producto->precio * $item['cantidad'];
                    $cartItems[] = [
                        'idproducto' => $idproducto,
                        'nombre' => $producto->nombre,
                        'precio' => $producto->precio,
                        'cantidad' => $item['cantidad'],
                        'subtotal' => $subtotal
                    ];
                    $total += $subtotal;
                }
            }
        }

        // Calcular descuentos de promociones activas
        $discounts = $this->getCartDiscounts($cartItems);
        $totalDiscount = collect($discounts)->sum('monto');
        $totalConDescuento = max(0, $total - $totalDiscount);

        return view('carrito.index', compact('cartItems', 'total', 'discounts', 'totalDiscount', 'totalConDescuento'));
    }

    public function add(Request $request)
    {
        $idproducto = $request->input('idproducto');
        $cantidad = max(1, (int)$request->input('cantidad', 1));
        
        $producto = Producto::findOrFail($idproducto);

        // Check if adding this quantity exceeds available stock
        $currentCartQuantity = 0;
        if (Auth::check()) {
            $ci = Auth::user()->ci;
            $dbItem = Carrito::where('ci_usuario', $ci)->where('idproducto', $idproducto)->first();
            if ($dbItem) $currentCartQuantity = $dbItem->cantidad;
        } else {
            $cart = session()->get('carrito', []);
            if (isset($cart[$idproducto])) $currentCartQuantity = $cart[$idproducto]['cantidad'];
        }

        if ($currentCartQuantity + $cantidad > $producto->cantidad) {
            $availableToAdd = max(0, $producto->cantidad - $currentCartQuantity);
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuficiente. Solo puedes añadir ' . $availableToAdd . ' unidad(es) más.'
                ]);
            }
            return redirect()->back()->with('error', 'Stock insuficiente.');
        }

        if (Auth::check()) {
            $ci = Auth::user()->ci;
            $dbItem = Carrito::where('ci_usuario', $ci)->where('idproducto', $idproducto)->first();
            
            if ($dbItem) {
                $dbItem->cantidad += $cantidad;
                $dbItem->save();
            } else {
                Carrito::create([
                    'ci_usuario' => $ci,
                    'idproducto' => $idproducto,
                    'cantidad' => $cantidad
                ]);
            }
        } else {
            $cart = session()->get('carrito', []);
            if (isset($cart[$idproducto])) {
                $cart[$idproducto]['cantidad'] += $cantidad;
            } else {
                $cart[$idproducto] = [
                    'cantidad' => $cantidad
                ];
            }
            session()->put('carrito', $cart);
        }

        if ($request->ajax()) {
            $cartCount = Auth::check() 
                ? Carrito::where('ci_usuario', Auth::user()->ci)->sum('cantidad')
                : collect(session()->get('carrito', []))->sum('cantidad');
                
            return response()->json([
                'success' => true, 
                'cartCount' => $cartCount
            ]);
        }

        return redirect()->back()->with('success', 'Producto agregado al carrito.');
    }

    public function update(Request $request)
    {
        $idproducto = $request->input('idproducto');
        $cantidad = max(1, (int)$request->input('cantidad', 1));

        $producto = Producto::findOrFail($idproducto);

        // Check if requested quantity exceeds available stock
        if ($cantidad > $producto->cantidad) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuficiente. Solo hay ' . $producto->cantidad . ' unidad(es) disponibles.',
                    'revertTo' => $producto->cantidad // Opcional, para revertir el input si quisiéramos
                ]);
            }
            return redirect()->back()->with('error', 'Stock insuficiente.');
        }

        if (Auth::check()) {
            $ci = Auth::user()->ci;
            $dbItem = Carrito::where('ci_usuario', $ci)->where('idproducto', $idproducto)->first();
            if ($dbItem) {
                $dbItem->cantidad = $cantidad;
                $dbItem->save();
            }
        } else {
            $cart = session()->get('carrito', []);
            if (isset($cart[$idproducto])) {
                $cart[$idproducto]['cantidad'] = $cantidad;
                session()->put('carrito', $cart);
            }
        }

        if ($request->ajax()) {
            $cartCount = 0;
            $total = 0;
            $subtotal = 0;

            if (Auth::check()) {
                $cartCount = Carrito::where('ci_usuario', Auth::user()->ci)->sum('cantidad');
                $items = Carrito::with('producto')->where('ci_usuario', Auth::user()->ci)->get();
                foreach ($items as $item) {
                    if ($item->producto) {
                        $itemSub = $item->producto->precio * $item->cantidad;
                        $total += $itemSub;
                        if ($item->idproducto == $idproducto) $subtotal = $itemSub;
                    }
                }
            } else {
                $cart = session()->get('carrito', []);
                foreach ($cart as $id => $item) {
                    $cartCount += $item['cantidad'];
                    $prod = Producto::find($id);
                    if ($prod) {
                        $itemSub = $prod->precio * $item['cantidad'];
                        $total += $itemSub;
                        if ($id == $idproducto) $subtotal = $itemSub;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'cartCount' => $cartCount,
                'subtotal' => number_format($subtotal, 2, '.', ''),
                'total' => number_format($total, 2, '.', '')
            ]);
        }

        return redirect()->route('carrito.index')->with('success', 'Carrito actualizado.');
    }

    public function remove(Request $request)
    {
        $idproducto = $request->input('idproducto');

        if (Auth::check()) {
            Carrito::where('ci_usuario', Auth::user()->ci)->where('idproducto', $idproducto)->delete();
        } else {
            $cart = session()->get('carrito', []);
            if (isset($cart[$idproducto])) {
                unset($cart[$idproducto]);
                session()->put('carrito', $cart);
            }
        }

        if ($request->ajax()) {
            $cartCount = 0;
            $total = 0;

            if (Auth::check()) {
                $cartCount = Carrito::where('ci_usuario', Auth::user()->ci)->sum('cantidad');
                $items = Carrito::with('producto')->where('ci_usuario', Auth::user()->ci)->get();
                foreach ($items as $item) {
                    if ($item->producto) {
                        $total += $item->producto->precio * $item->cantidad;
                    }
                }
            } else {
                $cart = session()->get('carrito', []);
                foreach ($cart as $id => $item) {
                    $cartCount += $item['cantidad'];
                    $prod = Producto::find($id);
                    if ($prod) {
                        $total += $prod->precio * $item['cantidad'];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'cartCount' => $cartCount,
                'total' => number_format($total, 2, '.', '')
            ]);
        }

        return redirect()->route('carrito.index')->with('success', 'Producto eliminado del carrito.');
    }

    public function clear()
    {
        if (Auth::check()) {
            Carrito::where('ci_usuario', Auth::user()->ci)->delete();
        } else {
            session()->forget('carrito');
        }

        return redirect()->route('carrito.index')->with('success', 'Carrito vaciado.');
    }

    public function checkout()
    {
        $this->mergeSessionCart();

        // Verificar que hay items
        $items = Carrito::with('producto')->where('ci_usuario', Auth::user()->ci)->get();
        if ($items->isEmpty()) {
            return redirect()->route('carrito.index')->with('error', 'Tu carrito está vacío.');
        }

        DB::beginTransaction();

        try {
            // Calcular totales y descuentos
            $cartItems = [];
            $total = 0;

            foreach ($items as $item) {
                if ($item->producto) {
                    $subtotal = $item->producto->precio * $item->cantidad;
                    $cartItems[] = [
                        'idproducto' => $item->idproducto,
                        'nombre' => $item->producto->nombre,
                        'precio' => $item->producto->precio,
                        'cantidad' => $item->cantidad,
                        'subtotal' => $subtotal
                    ];
                    $total += $subtotal;
                }
            }

            $discounts = $this->getCartDiscounts($cartItems);
            $totalDiscount = collect($discounts)->sum('monto');
            $totalConDescuento = max(0, $total - $totalDiscount);

            // Asegurar que el usuario tenga un registro en la tabla 'cliente'
            $ci = Auth::user()->ci;
            Cliente::firstOrCreate(['ci' => $ci], [
                'categoria' => 'Regular'
            ]);

            // Obtener el siguiente número correlativo para la factura
            $ultimoNro = FacturaVenta::max('nro') ?? 0;
            $nroFactura = $ultimoNro + 1;

            // Crear la Factura de Venta (NotaVenta)
            $factura = FacturaVenta::create([
                'nro' => $nroFactura,
                'fecha' => now(),
                'total' => $totalConDescuento,
                'ci_cliente' => $ci,
                'ci_empleado' => null, // Compra online realizada por el cliente
                'id_pago' => 2 // Tarjeta de Débito / PayPal
            ]);

            // Descontar la cantidad de stock en cada producto y crear detalles
            foreach ($items as $item) {
                $producto = Producto::find($item->idproducto);
                if ($producto) {
                    if ($producto->cantidad < $item->cantidad) {
                        throw new \Exception("Stock insuficiente para el producto: {$producto->nombre}.");
                    }
                    // Previene que quede en negativo
                    $producto->cantidad -= $item->cantidad;
                    $producto->save();

                    // Crear detalle de venta
                    DetalleFacturaVenta::create([
                        'nro_factura' => $nroFactura,
                        'id_producto' => $item->idproducto,
                        'precio_unitario' => $producto->precio,
                        'cantidad' => $item->cantidad,
                        'descuento' => 0.00
                    ]);
                }
            }

            // Vaciar el carrito ya que la compra fue "hecha"
            Carrito::where('ci_usuario', Auth::user()->ci)->delete();

            // Registrar en la Bitácora
            Bitacora::registrar(
                'INSERTAR',
                'notaventa',
                $nroFactura,
                "Compra online realizada con éxito por total de " . number_format($totalConDescuento, 2) . " BOB. Cliente CI: " . Auth::user()->ci
            );

            DB::commit();

            return view('carrito.success');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('carrito.index')->with('error', 'Error al procesar la compra: ' . $e->getMessage());
        }
    }

    public function generarCotizacion()
    {
        $this->mergeSessionCart();

        $cartItems = [];
        $total = 0;

        if (Auth::check()) {
            $items = Carrito::with('producto')->where('ci_usuario', Auth::user()->ci)->get();
            foreach ($items as $item) {
                if ($item->producto) {
                    $subtotal = $item->producto->precio * $item->cantidad;
                    $cartItems[] = [
                        'idproducto' => $item->idproducto,
                        'nombre' => $item->producto->nombre,
                        'precio' => $item->producto->precio,
                        'cantidad' => $item->cantidad,
                        'subtotal' => $subtotal
                    ];
                    $total += $subtotal;
                }
            }
        } else {
            $sessionCart = session()->get('carrito', []);
            foreach ($sessionCart as $idproducto => $item) {
                $producto = Producto::find($idproducto);
                if ($producto) {
                    $subtotal = $producto->precio * $item['cantidad'];
                    $cartItems[] = [
                        'idproducto' => $idproducto,
                        'nombre' => $producto->nombre,
                        'precio' => $producto->precio,
                        'cantidad' => $item['cantidad'],
                        'subtotal' => $subtotal
                    ];
                    $total += $subtotal;
                }
            }
        }

        if (empty($cartItems)) {
            return redirect()->route('carrito.index')->with('error', 'Tu carrito está vacío, no se puede generar la cotización.');
        }

        // Calcular descuentos de promociones activas
        $discounts = $this->getCartDiscounts($cartItems);
        $totalDiscount = collect($discounts)->sum('monto');
        $totalConDescuento = max(0, $total - $totalDiscount);

        return view('cotizacion.imprimir', compact('cartItems', 'total', 'discounts', 'totalDiscount', 'totalConDescuento'));
    }

    /**
     * Guardar el carrito actual como una cotización persistente.
     */
    public function guardarCotizacion(Request $request)
    {
        $this->mergeSessionCart();

        $ci = Auth::user()->ci;
        $items = Carrito::with('producto')->where('ci_usuario', $ci)->get();

        if ($items->isEmpty()) {
            return redirect()->route('carrito.index')->with('error', 'Tu carrito está vacío. No se puede guardar una cotización.');
        }

        // Calcular items y total
        $cartItems = [];
        $total = 0;

        foreach ($items as $item) {
            if ($item->producto) {
                $subtotal = $item->producto->precio * $item->cantidad;
                $cartItems[] = [
                    'idproducto' => $item->idproducto,
                    'nombre' => $item->producto->nombre,
                    'precio' => $item->producto->precio,
                    'cantidad' => $item->cantidad,
                    'subtotal' => $subtotal
                ];
                $total += $subtotal;
            }
        }

        // Aplicar descuentos de promociones activas
        $discounts = $this->getCartDiscounts($cartItems);
        $totalDiscount = collect($discounts)->sum('monto');
        $totalConDescuento = max(0, $total - $totalDiscount);

        DB::beginTransaction();
        try {
            // Crear cabecera de cotización
            $cotizacion = Cotizacion::create([
                'ci_cliente' => $ci,
                'fecha' => now()->toDateString(),
                'total' => $totalConDescuento,
                'observaciones' => $request->input('observaciones'),
            ]);

            // Crear detalles
            foreach ($items as $item) {
                if ($item->producto) {
                    CotizacionDetalle::create([
                        'cotizacion_id' => $cotizacion->id,
                        'idproducto' => $item->idproducto,
                        'cantidad' => $item->cantidad,
                        'precio_unitario' => $item->producto->precio,
                    ]);
                }
            }

            // Registrar en bitácora
            Bitacora::registrar(
                'INSERTAR',
                'cotizaciones',
                $cotizacion->id,
                "Cotización #{$cotizacion->id} guardada con total de " . number_format($totalConDescuento, 2) . " BOB. Cliente CI: {$ci}"
            );

            DB::commit();

            return redirect()->route('cotizaciones.guardadas')->with('success', '¡Cotización guardada exitosamente!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('carrito.index')->with('error', 'Error al guardar la cotización: ' . $e->getMessage());
        }
    }

    /**
     * Listar todas las cotizaciones guardadas del cliente autenticado.
     */
    public function verCotizacionesGuardadas()
    {
        $ci = Auth::user()->ci;
        $cotizaciones = Cotizacion::with('detalles.producto')
            ->where('ci_cliente', $ci)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cotizacion.guardadas', compact('cotizaciones'));
    }

    /**
     * Cargar una cotización guardada en el carrito activo.
     */
    public function cargarCotizacion($id)
    {
        $ci = Auth::user()->ci;
        $cotizacion = Cotizacion::with('detalles.producto')
            ->where('ci_cliente', $ci)
            ->findOrFail($id);

        DB::beginTransaction();
        try {
            // Vaciar el carrito actual
            Carrito::where('ci_usuario', $ci)->delete();

            // Insertar los productos de la cotización al carrito
            foreach ($cotizacion->detalles as $detalle) {
                if ($detalle->producto && $detalle->producto->cantidad > 0) {
                    $cantidadDisponible = min($detalle->cantidad, $detalle->producto->cantidad);
                    Carrito::create([
                        'ci_usuario' => $ci,
                        'idproducto' => $detalle->idproducto,
                        'cantidad' => $cantidadDisponible,
                    ]);
                }
            }

            // Registrar en bitácora
            Bitacora::registrar(
                'CONSULTAR',
                'cotizaciones',
                $cotizacion->id,
                "Cotización #{$cotizacion->id} cargada en el carrito. Cliente CI: {$ci}"
            );

            DB::commit();

            return redirect()->route('carrito.index')->with('success', '¡Cotización cargada en tu carrito! Revisa los productos.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cotizaciones.guardadas')->with('error', 'Error al cargar la cotización: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar una cotización guardada.
     */
    public function eliminarCotizacion($id)
    {
        $ci = Auth::user()->ci;
        $cotizacion = Cotizacion::where('ci_cliente', $ci)->findOrFail($id);

        $cotizacion->delete();

        Bitacora::registrar(
            'ELIMINAR',
            'cotizaciones',
            $id,
            "Cotización #{$id} eliminada. Cliente CI: {$ci}"
        );

        return redirect()->route('cotizaciones.guardadas')->with('success', 'Cotización eliminada correctamente.');
    }

    /**
     * Agregar todos los productos de una promoción/combo al carrito y redirigir.
     */
    public function addPromocion($id)
    {
        $promocion = Promocion::with('productos')->findOrFail($id);

        // Verificar que la promoción siga vigente
        if (!$promocion->estaVigente()) {
            return redirect()->route('inventario')->with('error', 'Esta promoción ya no está vigente.');
        }

        // Verificar stock de todos los productos
        foreach ($promocion->productos as $producto) {
            if ($producto->cantidad < 1) {
                return redirect()->route('inventario')->with('error', "El producto '{$producto->nombre}' no tiene stock disponible.");
            }
        }

        // Agregar cada producto al carrito
        foreach ($promocion->productos as $producto) {
            if (Auth::check()) {
                $ci = Auth::user()->ci;
                $dbItem = Carrito::where('ci_usuario', $ci)->where('idproducto', $producto->idproducto)->first();

                if ($dbItem) {
                    if ($dbItem->cantidad + 1 <= $producto->cantidad) {
                        $dbItem->cantidad += 1;
                        $dbItem->save();
                    }
                } else {
                    Carrito::create([
                        'ci_usuario' => $ci,
                        'idproducto' => $producto->idproducto,
                        'cantidad' => 1
                    ]);
                }
            } else {
                $cart = session()->get('carrito', []);
                $pid = $producto->idproducto;
                if (isset($cart[$pid])) {
                    if ($cart[$pid]['cantidad'] + 1 <= $producto->cantidad) {
                        $cart[$pid]['cantidad'] += 1;
                    }
                } else {
                    $cart[$pid] = ['cantidad' => 1];
                }
                session()->put('carrito', $cart);
            }
        }

        return redirect()->route('carrito.index')->with('success', "¡Promoción '{$promocion->nombre}' agregada al carrito!");
    }

    /**
     * Calcular descuentos dinámicos basados en promociones activas.
     * Retorna un array de descuentos aplicables.
     */
    private function getCartDiscounts(array $cartItems): array
    {
        $discounts = [];

        // IDs de productos en el carrito
        $cartProductIds = collect($cartItems)->pluck('idproducto')->toArray();

        if (empty($cartProductIds)) {
            return $discounts;
        }

        // Obtener promociones activas vigentes
        $promociones = Promocion::with('productos')
            ->where('estado', 'Activo')
            ->where('fecha_inicio', '<=', now())
            ->where('fecha_fin', '>=', now())
            ->get();

        foreach ($promociones as $promo) {
            $promoProductIds = $promo->productos->pluck('idproducto')->toArray();

            if ($promo->tipo === 'Combo') {
                // Verificar si TODOS los productos del combo están en el carrito
                $allPresent = empty(array_diff($promoProductIds, $cartProductIds));
                if ($allPresent && $promo->precio_combo !== null) {
                    // Calcular el precio original sumando los precios individuales
                    $precioOriginal = 0;
                    foreach ($cartItems as $item) {
                        if (in_array($item['idproducto'], $promoProductIds)) {
                            $precioOriginal += $item['precio']; // precio unitario × 1
                        }
                    }
                    $ahorro = $precioOriginal - $promo->precio_combo;
                    if ($ahorro > 0) {
                        $discounts[] = [
                            'nombre' => "Combo: {$promo->nombre}",
                            'descripcion' => "Precio combo: " . number_format($promo->precio_combo, 2) . " Bs.",
                            'monto' => round($ahorro, 2),
                        ];
                    }
                }
            } elseif ($promo->tipo === 'Global' && $promo->descuento_porcentaje > 0) {
                // Aplicar descuento porcentual a cada producto del carrito que esté en la promo
                $descuentoTotal = 0;
                foreach ($cartItems as $item) {
                    if (in_array($item['idproducto'], $promoProductIds)) {
                        $descuentoTotal += ($item['subtotal'] * $promo->descuento_porcentaje / 100);
                    }
                }
                if ($descuentoTotal > 0) {
                    $discounts[] = [
                        'nombre' => "{$promo->nombre} (-{$promo->descuento_porcentaje}%)",
                        'descripcion' => "Descuento aplicado a productos seleccionados",
                        'monto' => round($descuentoTotal, 2),
                    ];
                }
            }
        }

        return $discounts;
    }
}
