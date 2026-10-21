<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_comprobante',
        'estado',
        'created_at',
        'updated_at',
    ];

    const SERIES = [
        'FACTURA' => 'F001',
        'BOLETA' => 'B002',
        'NOTA VENTA' => 'NT00',
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
    public function emisionComprobante()
    {
        return $this->hasMany(EmisionComprobante::class);
    }

}
