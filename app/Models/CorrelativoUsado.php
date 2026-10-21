<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorrelativoUsado extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'correlativos_usados';

    /**
     * Clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indica si el modelo debe gestionar las marcas de tiempo.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Atributos que se pueden asignar masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'tipo_comprobante',
        'serie',
        'correlativo',
        'fecha',
    ];

    /**
     * Tipos de comprobante permitidos.
     */
    const TIPOS_COMPROBANTE = [
        'factura',
        'boleta',
        'nota_credito',
        'nota_debito',
    ];

    /**
     * Formatos válidos para 'tipo_comprobante'.
     *
     * @return array
     */
    public static function getTiposComprobante()
    {
        return self::TIPOS_COMPROBANTE;
    }

    /**
     * Obtener el siguiente correlativo disponible para un tipo de comprobante y serie específicos.
     *
     * @param string $tipoComprobante
     * @param string $serie
     * @return int
     */
    public static function getNextCorrelativo(string $tipoComprobante, string $serie): int
    {
        $ultimoCorrelativo = self::where('tipo_comprobante', $tipoComprobante)
            ->where('serie', $serie)
            ->orderBy('correlativo', 'desc')
            ->value('correlativo');

        return $ultimoCorrelativo ? $ultimoCorrelativo + 1 : 1;
    }

    /**
     * Relación con el modelo EmisionComprobante.
     * Asumiendo que cada correlativo usado está asociado a una emisión de comprobante.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emisionComprobantes()
    {
        return $this->hasMany(EmisionComprobante::class, 'correlativo_usado_id', 'id');
    }
}
