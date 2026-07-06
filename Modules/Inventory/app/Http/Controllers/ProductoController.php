<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Modules\Inventory\Models\Categoria;
use Modules\Inventory\Models\Marca;
use Modules\Inventory\Models\Producto;
use Modules\Sales\Models\Promocion;
use Modules\Audit\Models\Bitacora;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        if ($buscar) {
            // Crear una categoría virtual para contener los resultados de la búsqueda
            $categoriaVirtual = new Categoria();
            $categoriaVirtual->idcategoria = 9999;
            $categoriaVirtual->nombre = "Resultados de búsqueda para: \"{$buscar}\"";

            $productos = Producto::where('nombre', 'LIKE', "%{$buscar}%")
                            ->orWhere('descripcion', 'LIKE', "%{$buscar}%")
                            ->get();

            $categoriaVirtual->setRelation('productos', $productos);
            $categoriaVirtual->setRelation('subcategorias', collect());

            $categorias = collect([$categoriaVirtual]);
        } else {
            // 1. Cargamos las categorías raíces con sus productos y subcategorías
            $categorias = Categoria::whereNull('id_categoria_padre')
                            ->with(['subcategorias', 'productos'])
                            ->get();
        }
                        
        // 2. Cargamos todas las categorías para los select de los formularios
        $categorias_formulario = Categoria::all();

        // Cargamos las promociones vigentes
        $promociones = Promocion::with('productos')
            ->where('estado', 'Activo')
            ->where('fecha_inicio', '<=', now())
            ->where('fecha_fin', '>=', now())
            ->orderBy('created_at', 'desc')
            ->get();

        // 3. DETERMINAR VISTA SEGÚN ROL
        // Administrador
        if (Gate::allows('admin')) {
            return view('inventario.roles.admin', compact('categorias', 'categorias_formulario', 'promociones'));
        }

        // Almacenero
        if (Gate::allows('almacenero')) {
            return view('inventario.roles.almacenero', compact('categorias', 'categorias_formulario', 'promociones'));
        }

        // Público / Cliente
        return view('inventario.roles.cliente', compact('categorias', 'categorias_formulario', 'promociones'));
    }

    /**
     * Muestra el formulario para crear un nuevo producto.
     */
    public function create()
    {
        $categorias_formulario = Categoria::all();
        $marcas = Marca::all();
        return view('inventario.create', compact('categorias_formulario', 'marcas'));
    }

    /**
     * Guarda el nuevo producto.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'idproducto' => ['required', 'integer', 'unique:producto,idproducto'],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'imagen' => ['nullable', 'url', 'max:2048'],
            'precio' => ['required', 'numeric', 'min:0'],
            'cantidad' => ['required', 'integer', 'min:0'],
            'id_marca' => ['required', 'integer'],
            'id_categoria' => ['required', 'integer'],
        ]);

        $producto = Producto::create($validated);
        
        Bitacora::registrar('INSERTAR', 'producto', $producto->idproducto, "Creación de producto: {$producto->nombre}");

        return redirect()->route('inventario')->with('success', '¡Producto creado con éxito!');
    }

    /**
     * Muestra el formulario para editar un producto.
     */
    public function edit($id)
    {
        $producto = Producto::where('idproducto', $id)->firstOrFail();
        $categorias_formulario = Categoria::all();
        $marcas = Marca::all();
        return view('inventario.edit', compact('producto', 'categorias_formulario', 'marcas'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate(
            [
                 'nombre' => ['required', 'string', 'max:255'],
                 'descripcion' => ['nullable', 'string', 'max:500'],
                 'imagen' => ['nullable', 'url', 'max:2048'],
                 'precio' => ['required', 'numeric', 'min:0'],
                 'cantidad' => ['required', 'integer', 'min:0'],
                 'id_marca' => ['required', 'integer', 'exists:marca,id'],
                 'id_categoria' => ['required', 'integer', 'exists:categoria,idcategoria'],
                 'fechacaducidad' => ['nullable', 'date'],
                 'id_color' => ['nullable', 'integer'],
                 'id_medida' => ['nullable', 'integer'],
                 'id_volumen' => ['nullable', 'integer'],
             ],
            [
                'nombre.required' => 'El nombre del producto es obligatorio.',
                'precio.required' => 'El precio es obligatorio.',
                'cantidad.required' => 'La cantidad es obligatoria.',
                'id_marca.required' => 'El ID de marca es obligatorio.',
                'id_categoria.required' => 'Debes seleccionar una categoría.',
            ]
        );

        try {
            $producto = Producto::where('idproducto', $id)->firstOrFail();
            $producto->update($validated);

            // REGISTRO EN BITÁCORA
            Bitacora::registrar(
                'ACTUALIZAR',
                'producto',
                $producto->idproducto,
                "Se actualizó el producto: {$producto->nombre}"
            );

            return redirect()->back()->with('success', 'Producto actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'form_modificar' => 'No se pudo actualizar el producto. Verifica los datos ingresados e intenta nuevamente.',
                ]);
        }
    }

    public function getProducto($id)
    {
        $producto = Producto::where('idproducto', $id)->first();
        if ($producto) {
            return response()->json(['success' => true, 'producto' => $producto]);
        }
        return response()->json(['success' => false, 'message' => 'Producto no encontrado'], 404);
    }

    public function showPublic($id)
    {
        // ElOrFail valida que exista y lanza 404 si no.
        $producto = Producto::with(['categoria', 'marca', 'color', 'medida', 'volumen'])
                        ->where('idproducto', $id)
                        ->firstOrFail();

        return view('inventario.producto-detalle', compact('producto'));
    }

    public function destroy(string $id)
    {
        try {
            $producto = Producto::where('idproducto', $id)->firstOrFail();
            $nombre = $producto->nombre;

            // Al usar SoftDeletes en el modelo, esto no borra el registro de la DB,
            // solo le pone una fecha en 'deleted_at'. Así no se rompen las ventas/referencias.
            $producto->delete();

            // REGISTRO EN BITÁCORA
            Bitacora::registrar(
                'ELIMINAR',
                'producto',
                $id,
                "Se eliminó lógicamente el producto: {$nombre}"
            );

            return redirect()->route('inventario')->with('success', 'Producto eliminado exitosamente');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['form_eliminar' => 'No se pudo eliminar el producto. Error: ' . $e->getMessage()]);
        }
    }
}