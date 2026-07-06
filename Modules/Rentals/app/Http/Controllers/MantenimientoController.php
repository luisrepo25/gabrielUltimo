<?php

namespace Modules\Rentals\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Rentals\Models\Mantenimiento;
use Modules\Inventory\Models\Producto;
use Modules\Audit\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MantenimientoController extends Controller
{
    /**
     * Listado de mantenimientos
     */
    public function index()
    {
        $mantenimientos = Mantenimiento::with(['producto', 'responsable'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.mantenimientos.index', compact('mantenimientos'));
    }

    /**
     * Formulario para programar nuevo mantenimiento
     */
    public function create()
    {
        // Solo aceptar maquinas electricas (filtrar por palabras clave de herramientas electricas)
        $maquinarias = Producto::where(function($query) {
            $query->whereHas('categoria', function($q) {
                $q->where('nombre', 'like', '%electri%')
                  ->orWhere('nombre', 'like', '%eléctri%');
            })
            ->orWhere('nombre', 'like', '%electri%')
            ->orWhere('nombre', 'like', '%eléctri%')
            ->orWhere('nombre', 'like', '%amoladora%')
            ->orWhere('nombre', 'like', '%taladro%')
            ->orWhere('nombre', 'like', '%sierra%')
            ->orWhere('nombre', 'like', '%soldadora%')
            ->orWhere('nombre', 'like', '%compresor%')
            ->orWhere('nombre', 'like', '%generador%')
            ->orWhere('nombre', 'like', '%hidrolavadora%');
        })->orderBy('nombre', 'asc')->get();

        return view('admin.mantenimientos.create', compact('maquinarias'));
    }

    /**
     * Guardar nuevo mantenimiento
     */
    public function store(Request $request)
    {
        $request->validate([
            'idproducto' => 'required|exists:producto,idproducto',
            'cantidad' => 'required|integer|min:1',
            'tipo' => 'required|in:Preventivo,Correctivo',
            'descripcion' => 'required|string|max:500',
            'costo' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        DB::beginTransaction();
        try {
            $mantenimiento = Mantenimiento::create([
                'idproducto' => $request->idproducto,
                'cantidad' => $request->cantidad,
                'tipo' => $request->tipo,
                'descripcion' => $request->descripcion,
                'costo' => $request->costo,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'estado' => 'Programado',
                'ci_responsable' => Auth::user()->ci,
                'observaciones' => $request->observaciones,
            ]);

            $producto = Producto::where('idproducto', $request->idproducto)->firstOrFail();

            Bitacora::registrar('INSERTAR', 'mantenimientos', $mantenimiento->id, "Mantenimiento {$request->tipo} programado para herramienta: {$producto->nombre} (Cant: {$request->cantidad}). Costo: {$request->costo} BOB");

            DB::commit();

            return redirect()->route('admin.mantenimientos.index')
                ->with('success', 'Mantenimiento programado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Formulario para editar un mantenimiento
     */
    public function edit($id)
    {
        $mantenimiento = Mantenimiento::with('producto')->findOrFail($id);
        
        // Solo aceptar maquinas electricas
        $maquinarias = Producto::where(function($query) {
            $query->whereHas('categoria', function($q) {
                $q->where('nombre', 'like', '%electri%')
                  ->orWhere('nombre', 'like', '%eléctri%');
            })
            ->orWhere('nombre', 'like', '%electri%')
            ->orWhere('nombre', 'like', '%eléctri%')
            ->orWhere('nombre', 'like', '%amoladora%')
            ->orWhere('nombre', 'like', '%taladro%')
            ->orWhere('nombre', 'like', '%sierra%')
            ->orWhere('nombre', 'like', '%soldadora%')
            ->orWhere('nombre', 'like', '%compresor%')
            ->orWhere('nombre', 'like', '%generador%')
            ->orWhere('nombre', 'like', '%hidrolavadora%');
        })->orderBy('nombre', 'asc')->get();

        return view('admin.mantenimientos.edit', compact('mantenimiento', 'maquinarias'));
    }

    /**
     * Actualizar un mantenimiento
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1',
            'tipo' => 'required|in:Preventivo,Correctivo',
            'descripcion' => 'required|string|max:500',
            'costo' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'required|in:Programado,En curso,Finalizado',
        ]);

        DB::beginTransaction();
        try {
            $mantenimiento = Mantenimiento::findOrFail($id);
            $estadoAnterior = $mantenimiento->estado;

            $mantenimiento->update([
                'cantidad' => $request->cantidad,
                'tipo' => $request->tipo,
                'descripcion' => $request->descripcion,
                'costo' => $request->costo,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'estado' => $request->estado,
                'observaciones' => $request->observaciones,
            ]);

            Bitacora::registrar('ACTUALIZAR', 'mantenimientos', $mantenimiento->id, "Mantenimiento actualizado. Estado: {$estadoAnterior} → {$request->estado}");

            DB::commit();

            return redirect()->route('admin.mantenimientos.index')
                ->with('success', 'Mantenimiento actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage())->withInput();
        }
    }
}
