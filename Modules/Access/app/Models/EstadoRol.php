<?php

namespace Modules\Access\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoRol extends Model
{
    protected $table = 'estadoRol';
    public $timestamps = false;
    
    // Al ser una tabla pivote o con clave foránea sin ID autoincremental primario claro, lo deshabilitamos para Eloquent.
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = ['id_rol', 'ci_empleado', 'fechaInicio', 'fechaFin', 'estado'];

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'ci_empleado', 'ci');
    }
}
