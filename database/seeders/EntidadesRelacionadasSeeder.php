<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Persona;
use App\Models\Proveedor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EntidadesRelacionadasSeeder extends Seeder
{
    public function run()
    {
        // Crear Documentos si no existen
        $documentos = [
            ['tipo_documento' => 'DNI'],
            ['tipo_documento' => 'Pasaporte'],
            ['tipo_documento' => 'RUC'],
            ['tipo_documento' => 'Carnet Extranjería'],
        ];

        foreach ($documentos as $documento) {
            DB::table('documentos')->updateOrInsert(
                ['tipo_documento' => $documento['tipo_documento']],
                []
            );
        }

        $documentoRucId = DB::table('documentos')->where('tipo_documento', 'RUC')->value('id');

        // Crear Persona Cliente
        $personaCliente = Persona::firstOrCreate([
            'numero_documento' => '01234567891'
        ], [
            'razon_social' => 'Publico General 777',
            'direccion' => 'Av. Principal 123',
            'tipo_persona' => 'juridica',
            'documento_id' => $documentoRucId,
            'condicion' => 'regular',
            'estado_sunat' => 'Baja',
            'departamento' => 'Lima',
            'distrito' => 'Miraflores',
            'provincia' => 'Lima',
            'tipo_documento' => '6',
            'ubigeo' => '150110',
            'via_nombre' => 'Av. Principal',
            'via_tipo' => 'Avenida',
            'zona_tipo' => 'Zona A',
        ]);

        Cliente::firstOrCreate(['persona_id' => $personaCliente->id]);

        // Crear Persona Proveedor
        $personaProveedor = Persona::firstOrCreate([
            'numero_documento' => '01234567892'
        ], [
            'razon_social' => 'Proveedor General',
            'direccion' => 'Av. Principal 123',
            'tipo_persona' => 'juridica',
            'documento_id' => $documentoRucId,
            'condicion' => 'regular',
            'estado_sunat' => 'Baja',
            'departamento' => 'Lima',
            'distrito' => 'Miraflores',
            'provincia' => 'Lima',
            'tipo_documento' => '6',
            'ubigeo' => '150111',
            'via_nombre' => 'Av. Principal',
            'via_tipo' => 'Avenida',
            'zona_tipo' => 'Zona B',
        ]);

        Proveedor::firstOrCreate(['persona_id' => $personaProveedor->id]);
    }
}
