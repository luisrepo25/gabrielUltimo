<?php

namespace Modules\Rentals\Models;

use Illuminate\Database\Eloquent\Model;

class Maquinaria extends Model
{
    protected $table = 'maquinarias';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'precio_hora',
        'precio_dia',
        'garantia_sugerida',
        'estado',
    ];

    /**
     * Relación con los detalles del alquiler
     */
    public function detalles()
    {
        return $this->hasMany(AlquilerDetalle::class, 'maquinaria_id');
    }
}
