<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $table = 'color';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['id', 'nombre'];
}
