<?php

namespace Modules\Sales\Models;

use Modules\Access\Models\Usuario;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $fillable = [
        'user_id',
        'monto_apertura',
        'monto_cierre',
        'diferencia',
        'estado',
        'fecha_apertura',
        'fecha_cierre',
    ];

    public function user()
    {
        // La tabla cajas guarda el CI en user_id, por lo que relacionamos con Usuario
        return $this->belongsTo(Usuario::class, 'user_id', 'ci');
    }
}
