<?php

namespace Modules\Audit\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class IaReportController extends Controller
{
    public function index()
    {
        return view('bitacora.ia-reports');
    }

    public function consultar(Request $request)
    {
        $request->validate([
            'consulta' => 'required|string|max:500',
        ]);

        $consulta = $request->input('consulta');
        $apiKey = env('GROQ_API_KEY');

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'La API Key de Groq no está configurada. Por favor, añádela en el archivo .env como GROQ_API_KEY.'
            ], 500);
        }

        $systemPrompt = "You are an expert system that translates natural language questions in Spanish into MySQL SELECT queries for a hardware store database.

The database tables are:
1. `producto` (idproducto INT, nombre VARCHAR, descripcion TEXT, precio DECIMAL, cantidad INT, fechacaducidad DATE, id_marca INT, id_categoria INT, id_color INT, id_medida INT, id_volumen INT, costo DECIMAL, deleted_at TIMESTAMP)
2. `usuario` (ci INT, nombre VARCHAR, apellido VARCHAR, telefono INT, sexo CHAR, email VARCHAR, domicilio VARCHAR, tipoPersona VARCHAR)
3. `empleado` (ci INT, salario DECIMAL, estado VARCHAR) - Inherits from usuario by ci.
4. `cliente` (ci INT, puntos INT, categoria VARCHAR) - Inherits from usuario by ci.
5. `marca` (id INT, nombre VARCHAR)
6. `categoria` (idcategoria INT, nombre VARCHAR, descripcion TEXT, id_categoria_padre INT)
7. `NotaVenta` (nro INT, fecha DATETIME, total DECIMAL, ci_cliente INT, ci_empleado INT, id_pago INT)
8. `detalleNotaVenta` (nro_factura INT, id_producto INT, precio_unitario DECIMAL, cantidad INT, descuento DECIMAL)
9. `NotaCompra` (nro INT, fecha DATETIME, total DECIMAL, ci_proveedor INT, id_pago INT)
10. `detalleNotaCompra` (nro_factura INT, id_producto INT, precio_unitario DECIMAL, cantidad INT)
11. `bitacora` (id INT, accion VARCHAR, tabla VARCHAR, registro_id VARCHAR, descripcion TEXT, created_at TIMESTAMP)
12. `cajas` (id INT, user_id INT, monto_apertura DECIMAL, monto_cierre DECIMAL, diferencia DECIMAL, estado VARCHAR, fecha_apertura DATETIME, fecha_cierre DATETIME)
13. `alquileres` (id INT, ci_cliente INT, ci_empleado INT, fecha_inicio DATETIME, fecha_fin_estimada DATETIME, fecha_devolucion DATETIME, total_estimado DECIMAL, total_real DECIMAL, estado VARCHAR)
14. `devoluciones` (id INT, nro_factura INT, tipo VARCHAR, motivo TEXT, fecha DATE, estado VARCHAR, ci_empleado INT, observaciones TEXT)
15. `devolucion_detalles` (id INT, devolucion_id INT, idproducto INT, cantidad INT)
16. `maquinarias` (id INT, codigo VARCHAR, nombre VARCHAR, descripcion TEXT, precio_hora DECIMAL, precio_dia DECIMAL, garantia_sugerida DECIMAL, estado VARCHAR)
17. `alquiler_detalles` (id INT, alquiler_id INT, maquinaria_id INT, precio_unitario DECIMAL, tipo_tarifa VARCHAR, tiempo_rentado INT)
18. `mantenimientos` (id INT, maquinaria_id INT, tipo VARCHAR, descripcion TEXT, costo DECIMAL, fecha_inicio DATE, fecha_fin DATE, estado VARCHAR, ci_responsable INT, observaciones TEXT)
19. `pedidos_reabastecimiento` (id INT, ci_empleado INT, fecha DATE, estado VARCHAR, observaciones TEXT)
20. `pedido_reabastecimiento_detalles` (id INT, pedido_id INT, idproducto INT, cantidad_sugerida INT)
21. `promociones` (id INT, nombre VARCHAR, descripcion TEXT, tipo VARCHAR, descuento_porcentaje DECIMAL, precio_combo DECIMAL, fecha_inicio DATE, fecha_fin DATE, estado VARCHAR)
22. `promocion_productos` (id INT, promocion_id INT, idproducto INT)

Rules:
1. Generate ONLY a valid MySQL SELECT query.
2. DO NOT wrap the query in markdown (such as ```sql ... ```), do not include any explanatory text, do not write anything else. Just the query string.
3. Only generate SELECT queries. Never generate INSERT, UPDATE, DELETE, DROP, or other write queries.
4. Keep the table names exactly as defined above (case-sensitive).
5. For relationships, perform joins as necessary. E.g., to join producto and marca, use `producto.id_marca = marca.id`. E.g., to join NotaVenta and customer names, join `NotaVenta` with `usuario` on `ci_cliente = ci`.
6. Make column aliases user-friendly, like `SELECT nombre AS Producto, cantidad AS Stock FROM producto`.";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.1-8b-instant',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $consulta],
                ],
                'temperature' => 0.0,
            ]);

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de conexión con Groq: ' . $response->reason()
                ], 500);
            }

            $result = $response->json();
            $query = trim($result['choices'][0]['message']['content'] ?? '');

            // Limpieza de formato markdown de código
            $query = preg_replace('/^```(sql)?/i', '', $query);
            $query = preg_replace('/```$/', '', $query);
            $query = trim($query);

            // Validar de forma estricta que empiece con SELECT
            if (!str_starts_with(strtoupper($query), 'SELECT')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Por seguridad, solo se permiten consultas de tipo SELECT.',
                    'query' => $query
                ], 403);
            }

            // Ejecutar la consulta en la base de datos
            $results = DB::select($query);

            $columns = [];
            if (!empty($results)) {
                $columns = array_keys((array)$results[0]);
            }

            return response()->json([
                'success' => true,
                'query' => $query,
                'columns' => $columns,
                'data' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el reporte con IA.',
                'query' => $query ?? null,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
