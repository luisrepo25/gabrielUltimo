<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use SoftDeletes;
    protected $table = 'producto';
    protected $primaryKey = 'idproducto';
    public $timestamps = false;
    // Esto permite que Laravel inserte datos en estos campos
    protected $fillable = ['idproducto', 'nombre', 'descripcion', 'imagen', 'modelo', 'precio', 'costo', 'cantidad', 'id_categoria', 'id_marca', 'fechacaducidad', 'id_color', 'id_medida', 'id_volumen'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'idcategoria');
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'id_marca', 'id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'id_color', 'id');
    }

    public function medida()
    {
        return $this->belongsTo(Medida::class, 'id_medida', 'id');
    }

    public function volumen()
    {
        return $this->belongsTo(Volumen::class, 'id_volumen', 'id');
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