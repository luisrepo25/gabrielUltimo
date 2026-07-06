<?php

namespace Modules\Sales\Models;

use Modules\Access\Models\Usuario;

use Illuminate\Database\Eloquent\Model;

class FacturaVenta extends Model
{
    protected $table = 'NotaVenta';
    protected $primaryKey = 'nro';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'nro',
        'fecha',
        'total',
        'ci_cliente',
        'ci_empleado',
        'id_pago'
    ];

    /**
     * Relación con el cliente (Usuario)
     */
    public function cliente()
    {
        return $this->belongsTo(Usuario::class, 'ci_cliente', 'ci');
    }

    /**
     * Relación con el empleado (Usuario/Empleado)
     */
    public function empleado()
    {
        return $this->belongsTo(Usuario::class, 'ci_empleado', 'ci');
    }

    /**
     * Relación con el método de pago
     */
    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'id_pago', 'id');
    }

    /**
     * Detalles de la factura
     */
    public function detalles()
    {
        return $this->hasMany(DetalleFacturaVenta::class, 'nro_factura', 'nro');
    }
}
