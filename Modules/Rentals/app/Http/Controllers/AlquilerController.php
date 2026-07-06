<?php

namespace Modules\Rentals\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Rentals\Models\Alquiler;
use Modules\Rentals\Models\AlquilerDetalle;
use Illuminate\Support\Facades\Gate;
use Modules\Rentals\Models\Maquinaria;
use Modules\Sales\Models\MetodoPago;
use Modules\Access\Models\Usuario;
use Modules\Access\Models\Cliente;
use Modules\Audit\Models\Bitacora;
use Modules\Sales\Models\Caja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AlquilerController extends Controller
{
    public function index(Request $request)
    {
        if (Gate::denies('admin') && auth()->user()->tipoPersona === 'C') {
            abort(403, 'No autorizado');
        }

        $query = Alquiler::with(['cliente', 'empleado', 'metodoPago']);

        // Búsqueda por cliente (CI o Nombre)
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('cliente', function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellido', 'like', "%{$buscar}%")
                  ->orWhere('ci', 'like', "%{$buscar}%");
            });
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $alquileres = $query->orderBy('fecha_inicio', 'desc')->paginate(15);
        return view('alquileres.index', compact('alquileres'));
    }

    public function create()
    {
        if (Gate::denies('admin') && auth()->user()->tipoPersona === 'C') {
            abort(403, 'No autorizado');
        }

        $metodosPago = MetodoPago::all();
        $maquinarias = Maquinaria::where('estado', 'disponible')->get();

        return view('alquileres.create', compact('metodosPago', 'maquinarias'));
    }

    public function store(Request $request)
    {
        if (Gate::denies('admin') && auth()->user()->tipoPersona === 'C') {
            abort(403, 'No autorizado');
        }

        $request->validate([
            'ci_cliente' => 'required|exists:usuario,ci',
            'metodo_pago_id' => 'required|exists:metodoPago,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin_estimada' => 'required|date|after:fecha_inicio',
            'garantizado_con' => 'required|string|max:255',
            'monto_garantia' => 'required|numeric|min:0',
            'maquinarias' => 'required|array|min:1',
            'maquinarias.*.id' => 'required|exists:maquinarias,id',
            'maquinarias.*.tipo_tarifa' => 'required|in:hora,dia',
            'maquinarias.*.tiempo_rentado' => 'required|integer|min:1',
        ]);

        // Verificar si el cajero tiene una caja abierta
        $cajaAbierta = Caja::where('user_id', Auth::id())
            ->where('estado', 'abierta')
            ->first();

        if (!$cajaAbierta) {
            return response()->json([
                'success' => false,
                'message' => 'Debes realizar la apertura de la caja antes de registrar alquileres.'
            ], 422);
        }

        DB::beginTransaction();

        try {
            $totalEstimado = 0;
            $detalles = [];

            // Procesar las maquinarias y validar disponibilidad
            foreach ($request->maquinarias as $item) {
                $maq = Maquinaria::where('id', $item['id'])->lockForUpdate()->first();

                if (!$maq) {
                    throw new \Exception("La maquinaria con ID {$item['id']} no existe.");
                }

                if ($maq->estado !== 'disponible') {
                    throw new \Exception("La maquinaria '{$maq->nombre}' no está disponible actualmente.");
                }

                $precioUnitario = $item['tipo_tarifa'] === 'hora' ? $maq->precio_hora : $maq->precio_dia;
                $subtotal = $precioUnitario * $item['tiempo_rentado'];
                $totalEstimado += $subtotal;

                // Cambiar estado a alquilado
                $maq->estado = 'alquilado';
                $maq->save();

                $detalles[] = [
                    'maquinaria_id' => $maq->id,
                    'precio_unitario' => $precioUnitario,
                    'tipo_tarifa' => $item['tipo_tarifa'],
                    'tiempo_rentado' => $item['tiempo_rentado']
                ];
            }

            // Crear el registro de Alquiler
            $alquiler = Alquiler::create([
                'ci_cliente' => $request->ci_cliente,
                'ci_empleado' => Auth::user()->ci,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin_estimada' => $request->fecha_fin_estimada,
                'garantizado_con' => $request->garantizado_con,
                'monto_garantia' => $request->monto_garantia,
                'total_estimado' => $totalEstimado,
                'estado' => 'activo',
                'metodo_pago_id' => $request->metodo_pago_id,
                'observaciones' => $request->observaciones,
            ]);

            // Guardar los detalles
            foreach ($detalles as $det) {
                $det['alquiler_id'] = $alquiler->id;
                AlquilerDetalle::create($det);
            }

            // Registrar en la Bitácora
            Bitacora::registrar(
                'INSERTAR',
                'alquileres',
                $alquiler->id,
                "Se registró el alquiler #{$alquiler->id} para el cliente CI: {$alquiler->ci_cliente} por un total estimado de " . number_format($totalEstimado, 2) . " BOB."
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Alquiler registrado con éxito.',
                'alquiler_id' => $alquiler->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el alquiler: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        if (Gate::denies('admin') && auth()->user()->tipoPersona === 'C') {
            abort(403, 'No autorizado');
        }

        $alquiler = Alquiler::with(['cliente', 'empleado', 'metodoPago', 'detalles.maquinaria'])->findOrFail($id);
        return view('alquileres.show', compact('alquiler'));
    }

    public function registrarDevolucion($id, Request $request)
    {
        if (Gate::denies('admin') && auth()->user()->tipoPersona === 'C') {
            abort(403, 'No autorizado');
        }

        $alquiler = Alquiler::findOrFail($id);

        if ($alquiler->estado === 'completado') {
            return redirect()->back()->with('error', 'Este alquiler ya ha sido devuelto anteriormente.');
        }

        DB::beginTransaction();

        try {
            $fechaDevolucion = now();
            $totalReal = 0;

            // Recalcular montos reales basados en el tiempo transcurrido
            foreach ($alquiler->detalles as $detalle) {
                $maq = $detalle->maquinaria;

                // Devolver maquinaria al estado disponible
                if ($maq) {
                    $maq->estado = 'disponible';
                    $maq->save();
                }

                // Cálculo de tiempo transcurrido real
                $fechaInicio = Carbon::parse($alquiler->fecha_inicio);
                $diferencia = $fechaInicio->diffInHours($fechaDevolucion);

                if ($detalle->tipo_tarifa === 'dia') {
                    // Mínimo 1 día
                    $tiempoReal = max(1, ceil($diferencia / 24));
                } else {
                    // Mínimo 1 hora
                    $tiempoReal = max(1, ceil($diferencia));
                }

                $totalReal += $detalle->precio_unitario * $tiempoReal;

                // Guardar tiempo real en el detalle
                $detalle->tiempo_rentado = $tiempoReal;
                $detalle->save();
            }

            // Actualizar alquiler
            $alquiler->update([
                'fecha_devolucion' => $fechaDevolucion,
                'total_real' => $totalReal,
                'estado' => 'completado',
                'observaciones' => $request->observaciones ? $alquiler->observaciones . " | Devolución: " . $request->observaciones : $alquiler->observaciones
            ]);

            // Registrar en la Bitácora
            Bitacora::registrar(
                'ACTUALIZAR',
                'alquileres',
                $alquiler->id,
                "Se registró la devolución del alquiler #{$alquiler->id}. Total real liquidado: " . number_format($totalReal, 2) . " BOB."
            );

            DB::commit();

            return redirect()->route('alquileres.show', $alquiler->id)->with('success', 'Devolución registrada con éxito. La maquinaria ya está disponible.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al registrar la devolución: ' . $e->getMessage());
        }
    }

    public function imprimir($id)
    {
        if (Gate::denies('admin') && auth()->user()->tipoPersona === 'C') {
            abort(403, 'No autorizado');
        }

        $alquiler = Alquiler::with(['cliente', 'empleado', 'metodoPago', 'detalles.maquinaria'])->findOrFail($id);

        $pdf = Pdf::loadView('alquileres.comprobante', compact('alquiler'));
        return $pdf->download('comprobante_alquiler_' . $alquiler->id . '.pdf');
    }
}
