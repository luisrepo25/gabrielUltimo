<?php

namespace Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Access\Models\Cliente;
use Modules\Access\Models\Usuario;
use Modules\Inventory\Models\Producto;
use Modules\Sales\Models\MetodoPago;
use Modules\Sales\Models\FacturaVenta;
use Modules\Sales\Models\DetalleFacturaVenta;
use Modules\Sales\Models\Caja;
use Modules\Audit\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;

class VentaController extends Controller
{
    /**
     * Mostrar la lista de ventas del cajero o general si es administrador.
     */
    public function index()
    {
        $isAdmin = \Illuminate\Support\Facades\Gate::allows('admin');
        
        $query = FacturaVenta::with(['cliente', 'empleado', 'metodoPago']);

        if (!$isAdmin) {
            // Filtrar solo por las ventas del cajero actual
            $query->where('ci_empleado', Auth::user()->ci);
        }

        $ventas = $query->orderBy('fecha', 'desc')->paginate(15);

        return view('ventas.index', compact('ventas', 'isAdmin'));
    }

    /**
     * Formulario de Nueva Venta (Punto de Venta / POS).
     */
    public function create()
    {
        // Obtener métodos de pago
        $metodosPago = MetodoPago::all();

        // Obtener productos disponibles en stock
        $productos = Producto::where('cantidad', '>', 0)->get();

        return view('ventas.create', compact('metodosPago', 'productos'));
    }

    /**
     * Buscar cliente por Cédula de Identidad (CI).
     */
    public function buscarCliente($ci)
    {
        $usuario = Usuario::where('ci', $ci)->first();

        if ($usuario) {
            // Si el usuario existe pero no tiene registro en cliente, lo creamos
            if ($usuario->tipoPersona !== 'C' && !$usuario->cliente) {
                Cliente::firstOrCreate([
                    'ci' => $usuario->ci
                ], [
                    'categoria' => null
                ]);
            }

            return response()->json([
                'success' => true,
                'cliente' => [
                    'ci' => $usuario->ci,
                    'nombre' => $usuario->nombre,
                    'apellido' => $usuario->apellido,
                    'telefono' => $usuario->telefono,
                    'email' => $usuario->email
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Cliente no encontrado.'
        ], 404);
    }

    /**
     * Registrar un cliente rápidamente vía AJAX.
     */
    public function registrarCliente(Request $request)
    {
        $request->validate([
            'ci' => 'required|integer|unique:usuario,ci',
            'nombre' => 'required|string|max:50',
            'apellido' => 'required|string|max:50',
            'sexo' => 'required|string|max:1',
            'telefono' => 'nullable|integer',
            'email' => 'required|email|max:150|unique:usuario,email',
            'domicilio' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Crear el usuario de tipo Cliente ('C')
            $usuario = Usuario::create([
                'ci' => $request->ci,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'sexo' => $request->sexo,
                'telefono' => $request->telefono,
                'email' => $request->email,
                'domicilio' => $request->domicilio,
                'tipoPersona' => 'C',
                'password' => Hash::make($request->ci) // Clave por defecto su CI
            ]);

            // Crear el perfil de Cliente
            Cliente::create([
                'ci' => $request->ci,
                'categoria' => 'Regular'
            ]);

            Bitacora::registrar('INSERTAR', 'usuario', $request->ci, "Registro rápido de cliente: {$request->nombre} {$request->apellido}");

            DB::commit();

            return response()->json([
                'success' => true,
                'cliente' => $usuario
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar al cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Almacenar la venta.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ci_cliente' => 'required|exists:usuario,ci',
            'id_pago' => 'required|exists:metodoPago,id',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:producto,idproducto',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.descuento' => 'nullable|numeric|min:0'
        ]);

        // Verificar si el cajero tiene una caja abierta
        $cajaAbierta = Caja::where('user_id', Auth::id())
            ->where('estado', 'abierta')
            ->first();

        if (!$cajaAbierta) {
            return response()->json([
                'success' => false,
                'message' => 'Debes realizar la apertura de la caja antes de registrar ventas.'
            ], 422);
        }

        DB::beginTransaction();

        try {
            // 1. Obtener el siguiente número correlativo para la factura
            $ultimoNro = FacturaVenta::max('nro') ?? 0;
            $nroFactura = $ultimoNro + 1;

            $totalFactura = 0;
            $detallesVenta = [];

            // 2. Procesar y verificar stock de los productos
            foreach ($request->productos as $item) {
                $producto = Producto::where('idproducto', $item['id'])->lockForUpdate()->first();

                if (!$producto) {
                    throw new \Exception("El producto con ID {$item['id']} no existe.");
                }

                if ($producto->cantidad < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para el producto: {$producto->nombre}. Stock disponible: {$producto->cantidad}.");
                }

                $descuento = isset($item['descuento']) ? (float)$item['descuento'] : 0.00;
                $precioUnitario = (float)$producto->precio;
                $subtotal = ($precioUnitario * $item['cantidad']) - $descuento;

                if ($subtotal < 0) {
                    throw new \Exception("El descuento no puede ser mayor que el precio total del producto.");
                }

                $totalFactura += $subtotal;

                // Descontar stock del producto
                $producto->cantidad -= $item['cantidad'];
                $producto->save();

                // Almacenar temporalmente los detalles
                $detallesVenta[] = [
                    'nro_factura' => $nroFactura,
                    'id_producto' => $producto->idproducto,
                    'precio_unitario' => $precioUnitario,
                    'cantidad' => $item['cantidad'],
                    'descuento' => $descuento
                ];
            }

            // 3. Crear la Factura de Venta
            $factura = FacturaVenta::create([
                'nro' => $nroFactura,
                'fecha' => now(),
                'total' => $totalFactura,
                'ci_cliente' => $request->ci_cliente,
                'ci_empleado' => Auth::user()->ci, // Registramos al empleado/cajero actual
                'id_pago' => $request->id_pago
            ]);

            // 4. Insertar los detalles de la venta
            foreach ($detallesVenta as $detalle) {
                DetalleFacturaVenta::create($detalle);
            }

            // 5. Registrar en bitácora
            Bitacora::registrar('INSERTAR', 'notaventa', $nroFactura, "Venta física registrada en tienda por total de " . number_format($totalFactura, 2) . " BOB. Cliente CI: {$request->ci_cliente}");

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta registrada con éxito.',
                'nro_factura' => $nroFactura
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la venta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar comprobante en PDF.
     */
    public function imprimir($nro)
    {
        $factura = FacturaVenta::with(['cliente', 'empleado', 'metodoPago', 'detalles.producto'])
            ->where('nro', $nro)
            ->firstOrFail();

        $pdf = Pdf::loadView('ventas.comprobante', compact('factura'));
        return $pdf->download('factura_venta_' . $factura->nro . '.pdf');
    }
}
