<?php

namespace App\Models;

// Importar las Facades necesarias
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasRoles, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'img',
        'estado',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function empresa()
    {
        return $this->hasOne(Empresa::class);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);  // CORRECCIÓN AQUÍ
    }

    public function gasto()
    {
        return $this->belongsTo(Gasto::class);
    }
    /**
     * Maneja la carga de imágenes del usuario y las almacena en la carpeta img_user.
     *
     * @param \Illuminate\Http\UploadedFile $image
     * @return string|null Nombre del archivo almacenado o null en caso de fallo
     * @throws \Exception
     */
    public function handleUploadImage($image)
    {
        try {
            // Verificar que se haya subido una imagen
            if (!$image) {
                throw new \Exception('No se ha proporcionado ninguna imagen.');
            }

            // Obtener el nombre original del archivo sin extensión y reemplazar espacios por guiones bajos
            $originalName = preg_replace('/\s+/', '_', pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME));
            // Generar un nombre único con la extensión .webp
            $name = time() . '_' . $originalName . '.webp';
            // Definir la ruta donde se guardará la imagen
            $imagePath = 'img_user/' . $name;

            // Convertir la imagen a .webp con una calidad del 90%
            $webpImage = Image::make($image)
                ->encode('webp', 90);

            // Guardar la imagen convertida en el almacenamiento público
            Storage::disk('public')->put($imagePath, (string) $webpImage);

            // Retornar solo el nombre de la imagen guardada
            return $name;
        } catch (\Exception $e) {
            // Registrar el error
            Log::error('Error al subir la imagen: ' . $e->getMessage());
            throw $e;
        }
    }
}
