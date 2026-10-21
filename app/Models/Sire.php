<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sire extends Model
{
    use HasFactory;

    // Nombre de la tabla (si no sigue la convención de pluralización)
    protected $table = 'sire';

    /**
     * Los campos que son asignables de forma masiva.
     *
     * @var array
     */
    protected $fillable = [
        'periodo',                  // Periodo tributario de la compra
        'fecha',                    // Fecha de registro
        'comprobante',              // Tipo de comprobante (Ej: factura, boleta)
        'numero_comprobante',       // Número de comprobante
        'proveedor',                // Nombre o razón social del proveedor
        'ruc_proveedor',            // RUC del proveedor
        'descripcion',              // Descripción del comprobante o compra
        'subtotal',                 // Subtotal antes de impuestos
        'igv',                      // Impuesto General a las Ventas
        'total',                    // Monto total de la compra
        'estado',                   // Estado del registro (Ej: activo, inactivo)
        'ticket_id',                // ID del ticket en el SIRE (para consultas de estado)
        'fecha_hora_registro',      // Fecha y hora de registro en el sistema
        'moneda',                   // Tipo de moneda (Ej: PEN, USD)
        'observaciones',            // Observaciones adicionales
    ];

    /**
     * Relaciones de la base de datos
     */

    // Ejemplo de relación uno a muchos con productos de la compra
    // public function productos()
    // {
    //     return $this->hasMany(SireProducto::class);
    // }
}
