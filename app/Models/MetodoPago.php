<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'descripcion', 'estado'
    ];

    /**
     * Relación: Un método de pago puede estar asociado a varias ventas.
     */
    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
    public function gasto()
    {
        return $this->belongsTo(Gasto::class, 'metodo_pagos_id');
    }
}
