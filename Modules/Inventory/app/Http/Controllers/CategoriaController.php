<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Inventory\Models\Categoria;
use Modules\Rentals\Models\Maquinaria;
use Modules\Inventory\Models\Producto;
use Modules\Audit\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    // ── PÚBLICA ────────────────────────────────────────────────────────────────

    public function indexPublic()
    {
        $categorias = Categoria::whereNull('id_categoria_padre')
            ->where('estado', true)
            ->withCount('productos')
            ->get()
            ->map(function ($cat) {
                // Sumar productos de subcategorías al conteo
                $subCount = Producto::whereIn(
                    'id_categoria',
                    $cat->subcategorias()->pluck('idcategoria')
                )->count();
                $cat->total_productos = $cat->productos_count + $subCount;
                return $cat;
            });

        return view('categorias.index', compact('categorias'));
    }

    public function productosPorCategoria($id, Request $request)
    {
        $categoria = Categoria::where('idcategoria', $id)->where('estado', true)->firstOrFail();

        // IDs de la categoría + sus subcategorías directas
        $subcatIds = $categoria->subcategorias()->pluck('idcategoria');
        $todosIds  = $subcatIds->prepend($categoria->idcategoria);

        $query = Producto::whereIn('id_categoria', $todosIds)->with(['categoria', 'marca']);

        if ($request->filled('subcategoria')) {
            $query->where('id_categoria', $request->subcategoria);
        }

        $orden = $request->get('orden', 'predeterminado');
        match ($orden) {
            'precio_asc'  => $query->orderBy('precio', 'asc'),
            'precio_desc' => $query->orderBy('precio', 'desc'),
            default       => $query->orderBy('idproducto', 'asc'),
        };

        $perPage  = (int) $request->get('mostrar', 16);
        $productos = $query->paginate($perPage)->withQueryString();

        $subcategorias = $categoria->subcategorias()
            ->where('estado', true)
            ->withCount('productos')
            ->get();

        $maquinasDisponibles = Maquinaria::where('estado', 'disponible')->count();

        return view('categorias.productos', compact('categoria', 'productos', 'subcategorias', 'orden', 'perPage', 'maquinasDisponibles'));
    }

    // ── ADMIN ──────────────────────────────────────────────────────────────────

    public function index()
    {
        $this->autorizarStaff();
        $categorias = Categoria::with('padre')->withCount('productos')->orderBy('id_categoria_padre')->get();
        return view('admin.categorias.index', compact('categorias'));
    }

    public function create()
    {
        $this->autorizarStaff();
        $padres = Categoria::whereNull('id_categoria_padre')->where('estado', true)->get();
        return view('admin.categorias.create', compact('padres'));
    }

    public function store(Request $request)
    {
        $this->autorizarStaff();

        $request->validate([
            'idcategoria'       => ['required', 'integer', 'unique:categoria,idcategoria'],
            'nombre'            => ['required', 'string', 'max:50'],
            'descripcion'       => ['nullable', 'string', 'max:255'],
            'id_categoria_padre'=> ['nullable', 'integer', 'exists:categoria,idcategoria'],
            'imagen'            => ['nullable', 'image', 'max:2048'],
            'estado'            => ['nullable', 'boolean'],
        ]);

        $imagenPath = null;
        if ($request->hasFile('imagen')) {
            $imagenPath = $request->file('imagen')->store('imagenes/categorias', 'public');
        }

        $cat = Categoria::create([
            'idcategoria'        => $request->idcategoria,
            'nombre'             => $request->nombre,
            'descripcion'        => $request->descripcion,
            'id_categoria_padre' => $request->id_categoria_padre ?: null,
            'imagen'             => $imagenPath,
            'estado'             => $request->boolean('estado', true),
        ]);

        Bitacora::registrar('INSERTAR', 'categoria', $cat->idcategoria, "Creación de categoría: {$cat->nombre}");

        return redirect()->route('admin.categorias.index')->with('success', 'Categoría creada con éxito.');
    }

    public function edit($id)
    {
        $this->autorizarStaff();
        $categoria = Categoria::findOrFail($id);
        $padres = Categoria::whereNull('id_categoria_padre')
            ->where('estado', true)
            ->where('idcategoria', '!=', $id)
            ->get();
        return view('admin.categorias.edit', compact('categoria', 'padres'));
    }

    public function update(Request $request, $id)
    {
        $this->autorizarStaff();
        $categoria = Categoria::findOrFail($id);

        $request->validate([
            'nombre'             => ['required', 'string', 'max:50'],
            'descripcion'        => ['nullable', 'string', 'max:255'],
            'id_categoria_padre' => ['nullable', 'integer', 'exists:categoria,idcategoria', Rule::notIn([$id])],
            'imagen'             => ['nullable', 'image', 'max:2048'],
            'estado'             => ['nullable', 'boolean'],
        ]);

        $imagenPath = $categoria->imagen;
        if ($request->hasFile('imagen')) {
            if ($categoria->imagen) {
                Storage::disk('public')->delete($categoria->imagen);
            }
            $imagenPath = $request->file('imagen')->store('imagenes/categorias', 'public');
        }

        $categoria->update([
            'nombre'             => $request->nombre,
            'descripcion'        => $request->descripcion,
            'id_categoria_padre' => $request->id_categoria_padre ?: null,
            'imagen'             => $imagenPath,
            'estado'             => $request->boolean('estado'),
        ]);

        Bitacora::registrar('ACTUALIZAR', 'categoria', $id, "Actualización de categoría: {$categoria->nombre}");

        return redirect()->route('admin.categorias.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy($id)
    {
        $this->autorizarStaff();
        $categoria = Categoria::findOrFail($id);

        $countProd = $categoria->productos()->count();
        if ($countProd > 0) {
            return redirect()->back()
                ->with('error', "No se puede eliminar: la categoría tiene {$countProd} productos activos.");
        }

        $countSub = $categoria->subcategorias()->where('estado', true)->count();
        if ($countSub > 0) {
            return redirect()->back()
                ->with('error', "No se puede eliminar: la categoría tiene {$countSub} subcategorías activas.");
        }

        if ($categoria->imagen) {
            Storage::disk('public')->delete($categoria->imagen);
        }

        Bitacora::registrar('ELIMINAR', 'categoria', $id, "Eliminación de categoría: {$categoria->nombre}");
        $categoria->delete();

        return redirect()->route('admin.categorias.index')->with('success', 'Categoría eliminada.');
    }

    private function autorizarStaff(): void
    {
        if (!Gate::allows('admin') && !Gate::allows('almacenero')) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
    }
}
