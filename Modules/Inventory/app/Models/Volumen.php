<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;

class Volumen extends Model
{
    protected $table = 'volumen';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['id', 'peso', 'volumen_m3'];
}
