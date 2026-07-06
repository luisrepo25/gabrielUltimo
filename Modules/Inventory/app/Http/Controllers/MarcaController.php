<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Inventory\Models\Marca;
use Modules\Inventory\Models\Categoria;
use Modules\Audit\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MarcaController extends Controller
{
    // ── PÚBLICA ────────────────────────────────────────────────────────────────

    public function indexPublic()
    {
        $marcas = Marca::where('estado', true)->get();
        return view('marcas.index', compact('marcas'));
    }

    public function productosPorMarca($id, Request $request)
    {
        $marca = Marca::where('id', $id)->where('estado', true)->firstOrFail();

        $query = $marca->productos()->with(['categoria', 'marca']);

        if ($request->filled('categoria')) {
            $query->where('id_categoria', $request->categoria);
        }

        $orden = $request->get('orden', 'predeterminado');
        match ($orden) {
            'precio_asc'  => $query->orderBy('precio', 'asc'),
            'precio_desc' => $query->orderBy('precio', 'desc'),
            default       => $query->orderBy('idproducto', 'asc'),
        };

        $perPage = (int) $request->get('mostrar', 16);
        $productos = $query->paginate($perPage)->withQueryString();

        // Categorías que tienen productos de esta marca (para el filtro lateral)
        $categoriaIds = $marca->productos()->pluck('id_categoria')->unique();
        $categorias = Categoria::whereIn('idcategoria', $categoriaIds)
            ->with('subcategorias')
            ->get();

        return view('marcas.productos', compact('marca', 'productos', 'categorias', 'orden', 'perPage'));
    }

    // ── ADMIN ──────────────────────────────────────────────────────────────────

    public function index()
    {
        Gate::authorize('admin');
        $marcas = Marca::withCount('productos')->get();
        return view('admin.marcas.index', compact('marcas'));
    }

    public function create()
    {
        Gate::authorize('admin');
        return view('admin.marcas.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('admin');

        $request->validate([
            'id'     => ['required', 'integer', 'unique:marca,id'],
            'nombre' => ['required', 'string', 'max:50', 'unique:marca,nombre'],
            'logo'   => ['required', 'image', 'max:2048'],
            'estado' => ['nullable', 'boolean'],
        ]);

        $logoPath = $request->file('logo')->store('logos/marcas', 'public');

        $marca = Marca::create([
            'id'     => $request->id,
            'nombre' => $request->nombre,
            'logo'   => $logoPath,
            'estado' => $request->boolean('estado', true),
        ]);

        Bitacora::registrar('INSERTAR', 'marca', $marca->id, "Creación de marca: {$marca->nombre}");

        return redirect()->route('admin.marcas.index')->with('success', 'Marca creada con éxito.');
    }

    public function edit($id)
    {
        Gate::authorize('admin');
        $marca = Marca::findOrFail($id);
        return view('admin.marcas.edit', compact('marca'));
    }

    public function update(Request $request, $id)
    {
        Gate::authorize('admin');
        $marca = Marca::findOrFail($id);

        $request->validate([
            'nombre' => ['required', 'string', 'max:50', Rule::unique('marca', 'nombre')->ignore($id)],
            'logo'   => ['nullable', 'image', 'max:2048'],
            'estado' => ['nullable', 'boolean'],
        ]);

        $logoPath = $marca->logo;
        if ($request->hasFile('logo')) {
            Storage::disk('public')->delete($marca->logo);
            $logoPath = $request->file('logo')->store('logos/marcas', 'public');
        }

        $marca->update([
            'nombre' => $request->nombre,
            'logo'   => $logoPath,
            'estado' => $request->boolean('estado'),
        ]);

        Bitacora::registrar('ACTUALIZAR', 'marca', $id, "Actualización de marca: {$marca->nombre}");

        return redirect()->route('admin.marcas.index')->with('success', 'Marca actualizada correctamente.');
    }

    public function destroy($id)
    {
        Gate::authorize('admin');
        $marca = Marca::findOrFail($id);

        $activos = $marca->productos()->count();
        if ($activos > 0) {
            return redirect()->back()
                ->with('error', "No se puede eliminar: la marca tiene {$activos} productos activos.");
        }

        if ($marca->logo) {
            Storage::disk('public')->delete($marca->logo);
        }

        Bitacora::registrar('ELIMINAR', 'marca', $id, "Eliminación de marca: {$marca->nombre}");
        $marca->delete();

        return redirect()->route('admin.marcas.index')->with('success', 'Marca eliminada.');
    }
}
