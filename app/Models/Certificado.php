<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class Certificado extends Model
{
    use HasFactory;

    protected $fillable = [
        'ruc',
        'certificado',
        'usuario_sunat',
        'password_sunat',
        'client_id_sire',
        'client_secret_sire',
        'estado',
    ];

    // Relación inversa con Empresa
    public function empresa()
    {
        return $this->hasOne(Empresa::class);
    }
    //
    public function manejarArchivoCertificado($file, $password = null)
    {
        // Verificar si el archivo es una instancia de UploadedFile
        if (!$file instanceof UploadedFile) {
            Log::error('certificado_archivo no es una instancia de UploadedFile.', [
                'file_type' => gettype($file),
                'file_content' => $file,
            ]);
            throw new \Exception('El archivo subido no es válido.');
        }

        // Obtener la extensión del archivo
        $extension = strtolower($file->getClientOriginalExtension());

        // Eliminar el certificado anterior si existe
        if ($this->certificado && Storage::disk('public')->exists('certificados/' . $this->certificado)) {
            Storage::disk('public')->delete('certificados/' . $this->certificado);
        }

        if ($extension === 'pem') {
            // Generar un nombre único para el archivo PEM
            $pemFileName = uniqid() . '.pem';
            $rutaArchivo = 'certificados/' . $pemFileName;

            // Almacenar el archivo PEM
            $file->storeAs('certificados', basename($pemFileName), 'public');

            // Asignar solo el nombre del archivo al campo 'certificado'
            $this->certificado = $pemFileName;

        } elseif ($extension === 'pfx') {
            // Manejar archivos PFX
            if (!$password) {
                Log::error('Se requiere una contraseña para el archivo PFX.');
                throw new \Exception('Se requiere una contraseña para el archivo PFX.');
            }

            // Leer el contenido del archivo PFX
            $pfxContent = file_get_contents($file->getRealPath());

            $certs = [];
            $res = openssl_pkcs12_read($pfxContent, $certs, $password);

            if (!$res) {
                Log::error('No se pudo leer el archivo PFX.', [
                    'file_path' => $file->getRealPath(),
                    'certs' => $certs,
                ]);
                throw new \Exception('No se pudo leer el archivo PFX. Verifica la contraseña y el archivo.');
            }

            $privateKey = $certs['pkey'] ?? null;
            $certificate = $certs['cert'] ?? null;
            $ca = $certs['extracerts'] ?? [];

            if (!$privateKey || !$certificate) {
                Log::error('El archivo PFX no contiene un certificado o clave privada válido.', [
                    'private_key' => $privateKey,
                    'certificate' => $certificate,
                ]);
                throw new \Exception('El archivo PFX no contiene un certificado o clave privada válido.');
            }

            // Combinar el certificado y la clave privada en un solo archivo PEM
            $pemContent = $certificate . "\n" . $privateKey;

            // Añadir certificados de la CA si existen
            foreach ($ca as $caCert) {
                $pemContent .= "\n" . $caCert;
            }

            // Generar un nombre único para el archivo PEM
            $pemFileName = uniqid() . '.pem';
            $rutaArchivo = 'certificados/' . $pemFileName;

            // Almacenar el archivo PEM
            Storage::disk('public')->put($rutaArchivo, $pemContent);

            // Asignar solo el nombre del archivo al campo 'certificado'
            $this->certificado = $pemFileName;

        } else {
            Log::error('Extensión de archivo de certificado no soportada.', [
                'extension' => $extension,
            ]);
            throw new \Exception('Tipo de archivo de certificado no soportado.');
        }
    }
}
