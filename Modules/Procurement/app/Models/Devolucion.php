<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Sales\Models\FacturaVenta;
use Modules\Access\Models\Usuario;

class Devolucion extends Model
{
    protected $table = 'devoluciones';

    protected $fillable = [
        'nro_factura',
        'tipo',
        'motivo',
        'fecha',
        'estado',
        'ci_empleado',
        'observaciones',
    ];

    /**
     * Relación con la factura de venta
     */
    public function factura()
    {
        return $this->belongsTo(FacturaVenta::class, 'nro_factura', 'nro');
    }

    /**
     * Relación con el empleado que gestionó la devolución
     */
    public function empleado()
    {
        return $this->belongsTo(Usuario::class, 'ci_empleado', 'ci');
    }

    /**
     * Detalles de los productos devueltos
     */
    public function detalles()
    {
        return $this->hasMany(DevolucionDetalle::class, 'devolucion_id');
    }
}
