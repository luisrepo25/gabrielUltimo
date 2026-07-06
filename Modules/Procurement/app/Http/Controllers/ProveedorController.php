<?php

namespace Modules\Procurement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Procurement\Models\Proveedor;
use Modules\Audit\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProveedorController extends Controller
{
    /**
     * Listado interactivo de proveedores y sus compras
     */
    public function index()
    {
        $proveedores = Proveedor::with(['compras.detalles.producto', 'compras.metodoPago'])
            ->orderBy('nombre', 'asc')
            ->get();

        return view('admin.proveedores.index', compact('proveedores'));
    }

    /**
     * Registrar un nuevo proveedor
     */
    public function store(Request $request)
    {
        $request->validate([
            'ci' => 'required|integer|unique:proveedor,ci',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'required|string',
            'telefono' => 'required|integer',
            'correo' => 'nullable|email|max:50',
            'direccion' => 'nullable|string|max:100',
        ], [
            'ci.unique' => 'El CI o NIT ingresado ya se encuentra registrado para otro proveedor.',
            'ci.required' => 'El CI o NIT es obligatorio.',
            'nombre.required' => 'El nombre del proveedor es obligatorio.',
            'telefono.required' => 'El número de teléfono es obligatorio.',
        ]);

        DB::beginTransaction();
        try {
            Proveedor::create([
                'ci' => $request->ci,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'telefono' => $request->telefono,
                'correo' => $request->correo,
                'direccion' => $request->direccion,
            ]);

            // Auditoría
            Bitacora::registrar('INSERTAR', 'proveedor', $request->ci, "Registro de proveedor: {$request->nombre} con CI: {$request->ci}");

            DB::commit();

            return redirect()->route('admin.proveedores.index')->with('success', 'Proveedor registrado con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al registrar al proveedor: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Actualizar los datos de un proveedor
     */
    public function update(Request $request, $ci)
    {
        $proveedor = Proveedor::where('ci', $ci)->firstOrFail();

        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'required|string',
            'telefono' => 'required|integer',
            'correo' => 'nullable|email|max:50',
            'direccion' => 'nullable|string|max:100',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'telefono.required' => 'El teléfono es obligatorio.',
        ]);

        DB::beginTransaction();
        try {
            $proveedor->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'telefono' => $request->telefono,
                'correo' => $request->correo,
                'direccion' => $request->direccion,
            ]);

            // Auditoría
            Bitacora::registrar('ACTUALIZAR', 'proveedor', $ci, "Actualización de datos del proveedor: {$request->nombre}");

            DB::commit();

            return redirect()->route('admin.proveedores.index')->with('success', 'Proveedor actualizado con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar al proveedor: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar un proveedor
     */
    public function destroy($ci)
    {
        $proveedor = Proveedor::where('ci', $ci)->firstOrFail();

        // Verificar si tiene notas de compra asociadas
        if ($proveedor->compras()->count() > 0) {
            return redirect()->route('admin.proveedores.index')->with('error', 'No se puede eliminar el proveedor porque tiene notas de compra registradas a su nombre.');
        }

        DB::beginTransaction();
        try {
            $nombre = $proveedor->nombre;
            $proveedor->delete();

            // Auditoría
            Bitacora::registrar('ELIMINAR', 'proveedor', $ci, "Eliminación del proveedor: {$nombre}");

            DB::commit();

            return redirect()->route('admin.proveedores.index')->with('success', 'Proveedor eliminado con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.proveedores.index')->with('error', 'Error al eliminar al proveedor: ' . $e->getMessage());
        }
    }
}
