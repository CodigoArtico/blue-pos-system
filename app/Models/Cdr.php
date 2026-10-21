<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cdr extends Model
{
    use HasFactory;

    protected $table = 'cdr';

    protected $fillable = [
        'emision_comprobante_id', 'estado', 'codigo_respuesta', 
        'mensaje', 'archivo_cdr', 'archivo_zip', 
        'contenido_xml', 'fecha_respuesta'
    ];

    // Relación: Pertenece a un comprobante
    public function comprobante()
    {
        return $this->belongsTo(Comprobante::class);
    }
}
