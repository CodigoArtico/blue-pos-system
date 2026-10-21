<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $fillable = ['tipo_documento'];

    public function personas()
    {
        return $this->hasMany(Persona::class);
    }
}
