<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = [
        'comprobante_id',
        'user_id',
        'cliente_id',
        'metodo_pago_id',
        'serie',
        'correlativo',
        'numero_comprobante',
        'identificador_web',
        'fecha_hora',
        'total',
        'impuesto',
        'monto_recibido',
        'numero_operacion',
        'estado'
    ];

    public function comprobante()
    {
        return $this->belongsTo(Comprobante::class);
    }

    public function ventasEstado()
    {
        return $this->hasOne(Venta_Estado::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class);
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_ventas')
            ->withPivot('cantidad', 'precio_ventas', 'descuento')
            ->withTimestamps();
    }

    public function productosVentas()
    {
        return $this->hasMany(ProductoVenta::class);
    }

    public function productosLibres()
    {
        return $this->hasMany(Producto_Libre::class);
    }

    public function emisionComprobante()
    {
        return $this->hasOne(EmisionComprobante::class);
    }
}
