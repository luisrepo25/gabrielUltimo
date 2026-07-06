<?php

namespace Modules\Audit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class Bitacora extends Model
{
    protected $fillable = ['usuario', 'accion', 'tabla', 'registro_id', 'descripcion', 'ip'];

    public static function registrar($accion, $tabla, $registro_id, $descripcion)
    {
        self::create([
            'usuario' => Auth::user() ? Auth::user()->name : 'Invitado',
            'accion' => $accion,
            'tabla' => $tabla,
            'registro_id' => $registro_id,
            'descripcion' => $descripcion,
            'ip' => Request::ip(),
        ]);
    }
}
