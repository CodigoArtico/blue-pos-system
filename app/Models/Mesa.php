<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mesa extends Model
{
    use HasFactory;

    // Definir la tabla asociada
    protected $table = 'mesas';

    // Campos que pueden ser rellenados
    protected $fillable = ['codigo_mesa','nombre', 'estado', 'reserva', 'nombre_reserva', 'hora_reserva'];

    /**
     * Relación con productos.
     * 
     * Una mesa puede tener muchos productos asignados a través de una tabla intermedia.
     */
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'mesa_producto')
                    ->withPivot('cantidad') // Para acceder a la cantidad de productos en la relación
                    ->withTimestamps(); // Para que registre created_at y updated_at en la relación
    }
}
