<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categoria';
    protected $primaryKey = 'idcategoria';
    public $timestamps = false;
    protected $fillable = ['idcategoria', 'nombre', 'descripcion', 'id_categoria_padre', 'imagen', 'estado'];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_categoria', 'idcategoria');
    }

    public function subcategorias()
    {
        return $this->hasMany(Categoria::class, 'id_categoria_padre', 'idcategoria');
    }

    public function padre()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria_padre', 'idcategoria');
    }
}