<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $fillable = [
        'fecha_hora',
        'impuesto',
        'numero_comprobante',
        'total',
        'estado',
        'comprobante_id',
        'proveedore_id'
    ];

    public function comprobante()
    {
        return $this->belongsTo(Comprobante::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedore_id');
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'compra_productos')
            ->withPivot('cantidad', 'precio_compra', 'precio_venta')
            ->withTimestamps();
    }
}
