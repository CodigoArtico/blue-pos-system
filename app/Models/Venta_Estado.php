<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta_Estado extends Model
{
    //
    use HasFactory;

    // Definir la tabla asociada
    protected $table = 'ventas_estado';

    // Definir los atributos que se pueden asignar en masa
    protected $fillable = [
        'venta_id',
        'serie',
        'estado_respuesta_sunat',
    ];

    /**
     * Relación uno a uno con el modelo Venta.
     * Cada registro en ventas_estado está asociado a una venta.
     */
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
}
