<?php

namespace Modules\Rentals\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Models\Producto;
use Modules\Access\Models\Usuario;

class Mantenimiento extends Model
{
    protected $table = 'mantenimientos';

    protected $fillable = [
        'idproducto',
        'cantidad',
        'tipo',
        'descripcion',
        'costo',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'ci_responsable',
        'observaciones',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    /**
     * Relación con el producto (herramienta/maquinaria)
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idproducto', 'idproducto');
    }

    /**
     * Relación con el responsable (empleado/usuario)
     */
    public function responsable()
    {
        return $this->belongsTo(Usuario::class, 'ci_responsable', 'ci');
    }
}
