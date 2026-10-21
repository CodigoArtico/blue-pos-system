<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UserWeb extends Authenticatable
{
    use Notifiable;

    protected $table = 'users_web';

    protected $fillable = [
        'name',
        'email',
        'password',
        // 'direccion',
        // 'telefono',
        // otros campos si es necesario
    ];

    public function ordenes()
    {
        return $this->hasMany(Orden::class, 'user_web_id');
    }
}
