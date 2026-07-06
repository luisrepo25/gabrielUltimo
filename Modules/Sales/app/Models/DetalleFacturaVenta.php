<?php

namespace Modules\Sales\Models;

use Modules\Inventory\Models\Producto;

use Illuminate\Database\Eloquent\Model;

class DetalleFacturaVenta extends Model
{
    protected $table = 'detalleNotaVenta';
    
    // Al ser una tabla de detalles con clave compuesta, deshabilitamos la clave primaria por defecto
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nro_factura',
        'id_producto',
        'precio_unitario',
        'cantidad',
        'descuento'
    ];

    /**
     * Relación con la factura principal
     */
    public function factura()
    {
        return $this->belongsTo(FacturaVenta::class, 'nro_factura', 'nro');
    }

    /**
     * Relación con el producto vendido
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'idproducto');
    }
}
