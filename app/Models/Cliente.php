<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = ['persona_id'];

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
