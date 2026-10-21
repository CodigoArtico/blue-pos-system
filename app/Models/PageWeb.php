<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageWeb extends Model
{
    use HasFactory;

    protected $table = 'page_web'; // Especificar el nombre de la tabla si no sigue el nombre por convención

    protected $fillable = ['url_sistem','url_web','telefono','nombre', 'descripcion'];

    // Relación uno a muchos con Billboard
    public function billboards()
    {
        return $this->hasMany(Billboard::class, 'id_page');
    }
}
