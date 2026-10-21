<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresa';

    protected $fillable = [
        'user_id',
        'ruc_dni',
        'razon_social',
        'direccion_empresa',
        'ubigeo',
        'departamento',
        'provincia',
        'distrito',
        'img_account',
        'razon_social_comprobante',
        'telefono_comprobante',
        'direccion_comprobante',
        'subtitulo_principal_comprobante',
        'descripcion_aviso_comprobante',
        'despedida_comprobante',
        'certificado_id' // Usar el ID del certificado
    ];

    // Relación uno a uno con Certificado
    public function certificado()
    {
        return $this->belongsTo(Certificado::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
