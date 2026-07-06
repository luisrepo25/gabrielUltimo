<?php

namespace Modules\Rentals\Models;

use Illuminate\Database\Eloquent\Model;

class AlquilerDetalle extends Model
{
    protected $table = 'alquiler_detalles';

    protected $fillable = [
        'alquiler_id',
        'maquinaria_id',
        'precio_unitario',
        'tipo_tarifa',
        'tiempo_rentado',
    ];

    /**
     * Relación con el alquiler
     */
    public function alquiler()
    {
        return $this->belongsTo(Alquiler::class, 'alquiler_id');
    }

    /**
     * Relación con la maquinaria
     */
    public function maquinaria()
    {
        return $this->belongsTo(Maquinaria::class, 'maquinaria_id');
    }
}
