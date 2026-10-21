<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BajaComprobante extends Model
{
    use HasFactory;

    protected $fillable = [
        'emision_comprobante_id',
        'motivo',
        'fecha_baja',
        'ticket_baja',
        'estado_sunat',
        'hash_cpe',
    ];

    // Relación: Pertenece a un comprobante
    public function Emisioncomprobante()
    {
        return $this->belongsTo(EmisionComprobante::class);
    }
}
