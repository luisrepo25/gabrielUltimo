<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;

class Medida extends Model
{
    protected $table = 'medida';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['id', 'longitud', 'ancho', 'alto'];
}
