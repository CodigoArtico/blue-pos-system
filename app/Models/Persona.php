<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $fillable = [
        'razon_social', 'direccion', 'tipo_persona', 
        'estado', 'documento_id', 'numero_documento', 'condicion' , 'estado_sunat',
        'departamento', 'distrito', 'provincia', 'tipo_documento', 'ubigeo', 'via_nombre',
        'via_tipo', 'zona_tipo'
    ];

    public function documento()
    {
        return $this->belongsTo(Documento::class);
    }

    public function cliente()
    {
        return $this->hasOne(Cliente::class);
    }

    public function proveedor()
    {
        return $this->hasOne(Proveedor::class);
    }
}
