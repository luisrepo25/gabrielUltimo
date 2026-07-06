<?php

namespace Modules\Rentals\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Rentals\Models\Maquinaria;
use Modules\Audit\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class MaquinariaController extends Controller
{
    public function index()
    {
        // Solo administradores y almaceneros ven/gestionan maquinaria
        if (Gate::denies('admin') && auth()->user()->tipoPersona === 'C') {
            abort(403, 'No autorizado');
        }

        $maquinarias = Maquinaria::orderBy('codigo')->paginate(15);
        return view('maquinarias.index', compact('maquinarias'));
    }
    
    // Catálogo público: mostrar maquinarias disponibles para alquiler
    public function indexPublic()
    {
        $maquinarias = Maquinaria::where('estado', 'disponible')->orderBy('codigo')->get();
        return view('maquinarias.catalogo', compact('maquinarias'));
    }

    public function create()
    {
        Gate::authorize('admin');
        return view('maquinarias.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('admin');

        $request->validate([
            'codigo' => ['required', 'string', 'max:50', 'unique:maquinarias,codigo'],
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string'],
            'precio_hora' => ['required', 'numeric', 'min:0'],
            'precio_dia' => ['required', 'numeric', 'min:0'],
            'garantia_sugerida' => ['required', 'numeric', 'min:0'],
            'estado' => ['required', 'in:disponible,alquilado,mantenimiento'],
        ]);

        $maquinaria = Maquinaria::create($request->all());

        Bitacora::registrar('INSERTAR', 'maquinarias', $maquinaria->id, "Se registró la maquinaria: {$maquinaria->nombre} con código {$maquinaria->codigo}");

        return redirect()->route('maquinarias.index')->with('success', 'Maquinaria registrada con éxito.');
    }

    public function edit(Maquinaria $maquinaria)
    {
        Gate::authorize('admin');
        return view('maquinarias.edit', compact('maquinaria'));
    }

    public function update(Request $request, Maquinaria $maquinaria)
    {
        Gate::authorize('admin');

        $request->validate([
            'codigo' => ['required', 'string', 'max:50', Rule::unique('maquinarias', 'codigo')->ignore($maquinaria->id)],
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string'],
            'precio_hora' => ['required', 'numeric', 'min:0'],
            'precio_dia' => ['required', 'numeric', 'min:0'],
            'garantia_sugerida' => ['required', 'numeric', 'min:0'],
            'estado' => ['required', 'in:disponible,alquilado,mantenimiento'],
        ]);

        $maquinaria->update($request->all());

        Bitacora::registrar('ACTUALIZAR', 'maquinarias', $maquinaria->id, "Se actualizó la maquinaria: {$maquinaria->nombre} ({$maquinaria->codigo})");

        return redirect()->route('maquinarias.index')->with('success', 'Maquinaria actualizada con éxito.');
    }

    public function destroy(Maquinaria $maquinaria)
    {
        Gate::authorize('admin');

        // Verificar si la maquinaria está alquilada o tiene historial
        if ($maquinaria->estado === 'alquilado') {
            return redirect()->route('maquinarias.index')->with('error', 'No se puede eliminar la maquinaria porque se encuentra alquilada.');
        }

        if ($maquinaria->detalles()->count() > 0) {
            return redirect()->route('maquinarias.index')->with('error', 'No se puede eliminar la maquinaria porque tiene registros de alquiler asociados.');
        }

        Bitacora::registrar('ELIMINAR', 'maquinarias', $maquinaria->id, "Se eliminó la maquinaria: {$maquinaria->nombre} ({$maquinaria->codigo})");
        
        $maquinaria->delete();

        return redirect()->route('maquinarias.index')->with('success', 'Maquinaria eliminada con éxito.');
    }
}
