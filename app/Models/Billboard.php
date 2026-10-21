<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Storage;

class Billboard extends Model
{
    use HasFactory;

    protected $fillable = ['id_page', 'estado_url', 'img_url', 'estado_path', 'img_path'];

    // Función para manejar la subida de la imagen
    public function handleUploadImage($image)
    {
        $file = $image;
        $name = time() . '_' . $file->getClientOriginalName();
        Storage::putFileAs('public/billboards/', $file, $name);

        return $name;
    }

    // Para obtener la URL completa del archivo
    public function getImageUrlAttribute()
    {
        return asset('storage/billboards/' . $this->img_path);
    }
}
