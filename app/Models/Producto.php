<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManagerStatic as Image;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'stock',
        'fecha_vencimiento',
        'img_path',
        'img_url',
        'medida',
        'estado',
        'destacado',
        'marca_id',
        'presentacione_id'
    ];

    public function handleUploadImage($image)
    {
        try {
            // Obtener el nombre original del archivo y reemplazar espacios por guiones bajos
            $originalName = preg_replace('/\s+/', '_', pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME));
            // Generar un nombre único con la extensión .webp
            $name = time() . '_' . $originalName . '.webp';
            // Definir la ruta donde se guardará la imagen
            $imagePath = 'productos/' . $name;

            // Convertir la imagen a .webp con una calidad del 90%
            $webpImage = Image::make($image)
                ->encode('webp', 90);

            // Guardar la imagen convertida en el almacenamiento público
            Storage::disk('public')->put($imagePath, (string) $webpImage);

            // Retornar solo el nombre de la imagen guardada
            return $name;
        } catch (\Exception $e) {
            Log::error('Error al subir la imagen: ' . $e->getMessage());
            throw $e;
        }
    }

    // Relación: Un producto pertenece a una marca
    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    // Relación: Un producto pertenece a una presentación
    public function presentacion()
    {
        return $this->belongsTo(Presentacion::class, 'presentacione_id');
    }

    // Relación: Un producto puede pertenecer a varias categorías
    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'categoria_productos');
    }

    public function compras()
    {
        return $this->belongsToMany(Compra::class, 'compra_productos')->withTimestamps()
            ->withPivot('cantidad', 'precio_compra', 'precio_venta');
    }

    public function precioVentaReciente()
    {
        return $this->compras()->where('estado', 1) // Aseguramos que solo se consideren compras activas
            ->latest('compras.created_at') // Obtenemos la más reciente
            ->value('precio_venta');
    }
    public function precioCompraReciente()
    {
        return $this->compras()->where('estado', 1) // Aseguramos que solo se consideren compras activas
            ->latest('compras.created_at') // Obtenemos la más reciente
            ->value('precio_compra');
    }
}
