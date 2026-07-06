<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedor';
    protected $primaryKey = 'ci';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'ci',
        'nombre',
        'descripcion',
        'telefono',
        'correo',
        'direccion'
    ];

    /**
     * Relación con las notas de compra
     */
    public function compras()
    {
        return $this->hasMany(NotaCompra::class, 'ci_proveedor', 'ci');
    }
}
