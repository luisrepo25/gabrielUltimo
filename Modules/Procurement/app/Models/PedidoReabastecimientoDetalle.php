<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Models\Producto;

class PedidoReabastecimientoDetalle extends Model
{
    protected $table = 'pedido_reabastecimiento_detalles';

    protected $fillable = [
        'pedido_id',
        'idproducto',
        'cantidad_sugerida',
    ];

    /**
     * Relación con el pedido principal
     */
    public function pedido()
    {
        return $this->belongsTo(PedidoReabastecimiento::class, 'pedido_id');
    }

    /**
     * Relación con el producto
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idproducto', 'idproducto');
    }
}
