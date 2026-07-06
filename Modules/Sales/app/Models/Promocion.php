<?php

namespace Modules\Sales\Models;

use Modules\Inventory\Models\Producto;

use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    protected $table = 'promociones';

    protected $fillable = [
        'nombre',
        'descripcion',
        'imagen',
        'tipo',
        'descuento_porcentaje',
        'precio_combo',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    /**
     * Productos asociados a esta promoción
     */
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'promocion_productos', 'promocion_id', 'idproducto');
    }

    /**
     * Verificar si la promoción está vigente
     */
    public function estaVigente(): bool
    {
        return $this->estado === 'Activo'
            && $this->fecha_inicio <= now()
            && $this->fecha_fin >= now();
    }

    /**
     * Accessor para convertir enlaces de páginas de Unsplash en URLs de imagen directa.
     */
    public function getImagenAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }

        if (str_contains($value, 'unsplash.com') && !str_contains($value, 'images.unsplash.com')) {
            $path = parse_url($value, PHP_URL_PATH);
            if ($path) {
                $segments = explode('/', trim($path, '/'));
                $lastSegment = end($segments);
                $dashPos = strrpos($lastSegment, '-');
                $id = ($dashPos !== false) ? substr($lastSegment, $dashPos + 1) : $lastSegment;
                return "https://images.unsplash.com/photo-{$id}?w=1400&auto=format&fit=crop&q=80";
            }
        }

        return $value;
    }
}
