<?php

namespace Modules\Access\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'cliente';
    protected $primaryKey = 'ci';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['ci', 'puntos', 'categoria'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ci', 'ci');
    }
}
