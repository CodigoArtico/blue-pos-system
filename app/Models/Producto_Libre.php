<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto_Libre extends Model
{
    use HasFactory;

    protected $table = 'productos_libres';

    protected $fillable = [
        'venta_id',
        'nombre',
        'cantidad',
        'precio_venta',
        'descuento',
    ];

    // Relación con el modelo Venta
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
}
