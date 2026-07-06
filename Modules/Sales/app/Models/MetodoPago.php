<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    protected $table = 'metodoPago';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['id', 'nombre'];

    /**
     * Relación con las facturas que usaron este método de pago
     */
    public function facturas()
    {
        return $this->hasMany(FacturaVenta::class, 'id_pago', 'id');
    }
}
