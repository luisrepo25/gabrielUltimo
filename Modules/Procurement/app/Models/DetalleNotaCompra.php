<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Models\Producto;

class DetalleNotaCompra extends Model
{
    protected $table = 'detalleNotaCompra';
    
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nro_factura',
        'id_producto',
        'precio_unitario',
        'cantidad'
    ];

    /**
     * Relación con la nota de compra principal
     */
    public function compra()
    {
        return $this->belongsTo(NotaCompra::class, 'nro_factura', 'nro');
    }

    /**
     * Relación con el producto adquirido
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'idproducto');
    }
}
