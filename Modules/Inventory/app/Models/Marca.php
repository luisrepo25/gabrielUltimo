<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = 'marca';
    public $timestamps = false;
    protected $fillable = ['id', 'nombre', 'logo', 'estado'];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_marca', 'id');
    }
}
