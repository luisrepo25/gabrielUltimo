<?php

namespace Modules\Access\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $table = 'empleado';
    protected $primaryKey = 'ci';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['ci', 'salario', 'estado'];

    public function asignaciones()
    {
        return $this->hasMany(EstadoRol::class, 'ci_empleado', 'ci');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ci', 'ci');
    }
}
