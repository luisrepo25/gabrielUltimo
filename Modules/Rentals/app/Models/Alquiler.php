<?php

namespace Modules\Rentals\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Access\Models\Usuario;
use Modules\Sales\Models\MetodoPago;

class Alquiler extends Model
{
    protected $table = 'alquileres';

    protected $fillable = [
        'ci_cliente',
        'ci_empleado',
        'fecha_inicio',
        'fecha_fin_estimada',
        'fecha_devolucion',
        'garantizado_con',
        'monto_garantia',
        'total_estimado',
        'total_real',
        'estado',
        'metodo_pago_id',
        'observaciones',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin_estimada' => 'datetime',
        'fecha_devolucion' => 'datetime',
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
        return $this->belongsTo(MetodoPago::class, 'metodo_pago_id', 'id');
    }

    /**
     * Detalles del alquiler
     */
    public function detalles()
    {
        return $this->hasMany(AlquilerDetalle::class, 'alquiler_id');
    }
}
