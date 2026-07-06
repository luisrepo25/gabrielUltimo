<?php

namespace Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Sales\Models\Caja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class CajaController extends Controller
{
    public function index()
    {
        $cajas = Caja::with('user')->orderBy('created_at', 'desc')->paginate(10);
        
        foreach ($cajas as $caja) {
            if ($caja->estado === 'abierta') {
                try {
                    $ventas = DB::table('NotaVenta')
                                ->where('ci_empleado', $caja->user_id)
                                ->where('fecha', '>=', $caja->fecha_apertura)
                                ->sum('total');
                } catch (\Exception $e) {
                    $ventas = 0;
                }
                $caja->saldo_esperado = $caja->monto_apertura + $ventas;
                $caja->ventas_total = $ventas;
            }
        }

        return view('admin.caja.index', compact('cajas'));
    }

    public function apertura(Request $request)
    {
        $request->validate([
            'monto_apertura' => 'required|numeric|min:0'
        ]);

        $caja = new Caja();
        $caja->user_id = auth()->id(); // Usar el ID del administrador logueado
        $caja->monto_apertura = $request->monto_apertura;
        $caja->estado = 'abierta';
        $caja->fecha_apertura = now();
        $caja->save();

        return redirect()->route('caja.index')->with('success', 'Se genero la apertura de la caja con exito');
    }

    public function corte(Request $request, Caja $caja)
    {
        $request->validate([
            'monto_real' => 'required|numeric|min:0'
        ]);

        if ($caja->estado !== 'abierta') {
            return redirect()->back()->with('error_acceso', 'La caja ya está cerrada.');
        }

        try {
            $ventas = DB::table('NotaVenta')
                        ->where('ci_empleado', $caja->user_id)
                        ->where('fecha', '>=', $caja->fecha_apertura)
                        ->sum('total');
        } catch (\Exception $e) {
            $ventas = 0;
        }

        $saldo_esperado = $caja->monto_apertura + $ventas;
        $diferencia = $request->monto_real - $saldo_esperado;

        $caja->update([
            'monto_cierre' => $request->monto_real,
            'diferencia' => $diferencia,
            'estado' => 'cerrada',
            'fecha_cierre' => Carbon::now(),
        ]);

        return redirect()->route('caja.index')->with('success', 'Corte de caja realizado con éxito.');
    }

    public function reporte(Caja $caja)
    {
        try {
            $ventas = DB::table('NotaVenta')
                        ->where('ci_empleado', $caja->user_id)
                        ->where('fecha', '>=', $caja->fecha_apertura)
                        ->where('fecha', '<=', $caja->fecha_cierre ?? Carbon::now())
                        ->sum('total');
        } catch (\Exception $e) {
            $ventas = 0;
        }

        $fondo = $caja->monto_apertura;
        $efectivo_real = $caja->monto_cierre ?? 0;
        $diferencia = $caja->diferencia ?? 0;

        $faltante = $diferencia < 0 ? abs($diferencia) : 0;
        $sobrante = $diferencia > 0 ? $diferencia : 0;

        $pdf = Pdf::loadView('admin.caja.reporte', compact('caja', 'ventas', 'fondo', 'efectivo_real', 'faltante', 'sobrante'));
        return $pdf->download('corte_cajero_'.$caja->id.'.pdf');
    }
}
