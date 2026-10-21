<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    //

    // Tabla asociada al modelo (si no es plural o no coincide con el nombre)
    protected $table = 'gastos';

    // Campos que son asignables en masa
    protected $fillable = [
        'fecha',
        'descripcion',
        'monto',
        'metodo_pagos_id',
        'user_id',
        'estado',
    ];

    // Relación con el modelo MetodoPago
    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_pagos_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
