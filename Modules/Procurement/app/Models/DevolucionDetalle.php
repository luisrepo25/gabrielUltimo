<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Models\Producto;

class DevolucionDetalle extends Model
{
    protected $table = 'devolucion_detalles';

    protected $fillable = [
        'devolucion_id',
        'idproducto',
        'cantidad',
    ];

    /**
     * Relación con la devolución principal
     */
    public function devolucion()
    {
        return $this->belongsTo(Devolucion::class, 'devolucion_id');
    }

    /**
     * Relación con el producto devuelto
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idproducto', 'idproducto');
    }
}
