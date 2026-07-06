<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Access\Models\Usuario;

class PedidoReabastecimiento extends Model
{
    protected $table = 'pedidos_reabastecimiento';

    protected $fillable = [
        'ci_empleado',
        'fecha',
        'estado',
        'observaciones',
    ];

    /**
     * Relación con el empleado que solicitó el pedido
     */
    public function empleado()
    {
        return $this->belongsTo(Usuario::class, 'ci_empleado', 'ci');
    }

    /**
     * Detalles de los productos solicitados
     */
    public function detalles()
    {
        return $this->hasMany(PedidoReabastecimientoDetalle::class, 'pedido_id');
    }
}
