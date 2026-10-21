<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmisionComprobante extends Model
{
    protected $fillable = [
        'comprobante_id',
        'venta_id',
        'serie',
        'correlativo',
        'fecha_emision',
        'hash_cpe',
        'estado_sunat',
        'firma_digital'
    ];

    /**
     * Relación con Venta.
     */
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    /**
     * Relación con Comprobante.
     */
    public function comprobante()
    {
        return $this->belongsTo(Comprobante::class);
    }

    /**
     * Relación con Cdr.
     */
    public function cdrs()
    {
        return $this->hasMany(Cdr::class);
    }
    // En el modelo EmisionComprobante
    public function bajaComprobante()
    {
        return $this->hasOne(BajaComprobante::class, 'emision_comprobante_id');
    }
}
