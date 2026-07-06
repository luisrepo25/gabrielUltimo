<?php

namespace Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Sales\Models\Promocion;
use Modules\Inventory\Models\Producto;
use Modules\Audit\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromocionController extends Controller
{
    /**
     * Listado de promociones y combos
     */
    public function index()
    {
        // Actualizar automáticamente promociones expiradas
        Promocion::where('estado', 'Activo')
            ->where('fecha_fin', '<', now())
            ->update(['estado' => 'Expirado']);

        $promociones = Promocion::with('productos')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.promociones.index', compact('promociones'));
    }

    /**
     * Formulario para crear nueva promoción/combo
     */
    public function create()
    {
        $productos = Producto::orderBy('nombre', 'asc')->get();
        return view('admin.promociones.create', compact('productos'));
    }

    /**
     * Guardar nueva promoción/combo
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'tipo' => 'required|in:Global,Combo',
            'imagen' => 'nullable|url|max:2048',
            'descuento_porcentaje' => 'required_if:tipo,Global|numeric|min:0|max:100',
            'precio_combo' => 'required_if:tipo,Combo|nullable|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'productos' => 'required|array|min:1',
            'productos.*' => 'exists:producto,idproducto',
        ], [
            'productos.required' => 'Debes seleccionar al menos un producto para la promoción.',
            'productos.min' => 'Debes seleccionar al menos un producto.',
        ]);

        DB::beginTransaction();
        try {
            $promocion = Promocion::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'imagen' => $request->imagen,
                'tipo' => $request->tipo,
                'descuento_porcentaje' => $request->tipo === 'Global' ? $request->descuento_porcentaje : 0,
                'precio_combo' => $request->tipo === 'Combo' ? $request->precio_combo : null,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'estado' => 'Activo',
            ]);

            // Asignar productos a la promoción
            $promocion->productos()->attach($request->productos);

            Bitacora::registrar('INSERTAR', 'promociones', $promocion->id, "Promoción '{$request->nombre}' creada. Tipo: {$request->tipo}. Productos asociados: " . count($request->productos));

            DB::commit();

            return redirect()->route('admin.promociones.index')
                ->with('success', 'Promoción creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear la promoción: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Formulario para editar una promoción
     */
    public function edit($id)
    {
        $promocion = Promocion::with('productos')->findOrFail($id);
        $productos = Producto::orderBy('nombre', 'asc')->get();

        return view('admin.promociones.edit', compact('promocion', 'productos'));
    }

    /**
     * Actualizar una promoción
     */
    public function update(Request $request, $id)
    {
         $request->validate([
            'nombre' => 'required|string|max:100',
            'tipo' => 'required|in:Global,Combo',
            'imagen' => 'nullable|url|max:2048',
            'descuento_porcentaje' => 'required_if:tipo,Global|numeric|min:0|max:100',
            'precio_combo' => 'required_if:tipo,Combo|nullable|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'productos' => 'required|array|min:1',
            'productos.*' => 'exists:producto,idproducto',
            'estado' => 'required|in:Activo,Inactivo,Expirado',
        ]);

        DB::beginTransaction();
        try {
            $promocion = Promocion::findOrFail($id);

            $promocion->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'imagen' => $request->imagen,
                'tipo' => $request->tipo,
                'descuento_porcentaje' => $request->tipo === 'Global' ? $request->descuento_porcentaje : 0,
                'precio_combo' => $request->tipo === 'Combo' ? $request->precio_combo : null,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'estado' => $request->estado,
            ]);

            // Sincronizar productos
            $promocion->productos()->sync($request->productos);

            Bitacora::registrar('ACTUALIZAR', 'promociones', $promocion->id, "Promoción '{$request->nombre}' actualizada. Tipo: {$request->tipo}.");

            DB::commit();

            return redirect()->route('admin.promociones.index')
                ->with('success', 'Promoción actualizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Eliminar una promoción
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $promocion = Promocion::findOrFail($id);
            $nombre = $promocion->nombre;

            $promocion->productos()->detach();
            $promocion->delete();

            Bitacora::registrar('ELIMINAR', 'promociones', $id, "Promoción '{$nombre}' eliminada del sistema.");

            DB::commit();

            return redirect()->route('admin.promociones.index')
                ->with('success', 'Promoción eliminada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    /**
     * Vista pública de promociones para clientes
     */
    public function indexPublic()
    {
        $promociones = Promocion::with('productos')
            ->where('estado', 'Activo')
            ->where('fecha_inicio', '<=', now())
            ->where('fecha_fin', '>=', now())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.promociones.public', compact('promociones'));
    }
}
