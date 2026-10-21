<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'caracteristica_id',
    ];

    // Relación muchos a uno con Caracteristica
    public function caracteristica()
    {
        return $this->belongsTo(Caracteristica::class);
    }
}
