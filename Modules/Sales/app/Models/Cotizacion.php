<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Access\Models\Usuario;

class Cotizacion extends Model
{
    protected $table = 'cotizaciones';

    protected $fillable = [
        'ci_cliente',
        'fecha',
        'total',
        'observaciones',
    ];

    /**
     * Relación con los detalles de la cotización
     */
    public function detalles()
    {
        return $this->hasMany(CotizacionDetalle::class, 'cotizacion_id');
    }

    /**
     * Relación con el cliente (Usuario)
     */
    public function cliente()
    {
        return $this->belongsTo(Usuario::class, 'ci_cliente', 'ci');
    }
}
