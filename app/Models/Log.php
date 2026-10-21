<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'tabla_afectada',
        'descripcion',
        'user_id',
        'fecha',
    ];

    // Relación muchos a uno con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
