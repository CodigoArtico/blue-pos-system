<?php

namespace Database\Seeders;

use App\Models\Certificado;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Verificar si ya existe un registro en la tabla certificados
        if (Certificado::count() == 0) {
            // Crear un certificado
            $certificado = Certificado::create([
                'ruc' => '00000000000',
                'certificado' => '6724fb3c911a0.pem',
                'usuario_sunat' => 'usuario_sunat',
                'password_sunat' => 'password123',
                'estado' => '1',
            ]);
        } else {
            // Obtener el primer certificado existente
            $certificado = Certificado::first();
            Log::info('Un certificado ya existe. Usando certificado ID: ' . $certificado->id);
        }
        // Obtener el primer usuario
        $usuario = User::first();
        // Verificar si ya existe un registro en la tabla empresa
        if (Empresa::count() == 0) {
            // Crear la empresa asociada al certificado
            Empresa::create([
                'user_id' => $usuario->id,
                'ruc_dni' => '12345678901',
                'razon_social' => 'Razón Social S.A.C.',
                'direccion_empresa' => 'Av. Ejemplo 123',
                'img_account' => '',
                'ubigeo' => '-',
                'departamento' => '-',
                'provincia' => '-',
                'distrito' => '-',
                'razon_social_comprobante' => 'Razón Social S.A.C.',
                'telefono_comprobante' => '987654321',
                'direccion_comprobante' => 'Av. Comprobante 456',
                'subtitulo_principal_comprobante' => 'VENTAS AL POR MAYOR Y MENOR',
                'descripcion_aviso_comprobante' => 'Verifique el estado de su producto antes de retirarse de nuestra tienda. Una vez salida la mercadería no hay lugar a cambio ni devoluciones.',
                'despedida_comprobante' => '¡MUCHAS GRACIAS POR SU PREFERENCIA!',
                'certificado_id' => $certificado->id, // Usar el ID del certificado
            ]);
        } else {
            // Opcional: agregar un mensaje de log
            Log::info('Ya existe un registro en la tabla empresa. No se ha realizado ninguna inserción.');
        }
    }
}
