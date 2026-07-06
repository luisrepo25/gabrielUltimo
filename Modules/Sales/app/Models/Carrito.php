<?php

namespace Modules\Sales\Models;

use Modules\Access\Models\Usuario;
use Modules\Inventory\Models\Producto;

use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    protected $table = 'carritos';

    protected $fillable = [
        'ci_usuario',
        'idproducto',
        'cantidad',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ci_usuario', 'ci');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idproducto', 'idproducto');
    }
}
