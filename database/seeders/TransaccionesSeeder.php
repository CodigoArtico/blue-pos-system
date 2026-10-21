<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Compra;
use App\Models\CompraProducto;
use App\Models\MetodoPago;
use App\Models\Producto;
use App\Models\ProductoVenta;
use App\Models\Proveedor;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransaccionesSeeder extends Seeder
{
    public function run()
    {
        // Crear Métodos de Pago si no existen
        $mpagos = [
            ['nombre' => 'Efectivo'],
            ['nombre' => 'Yape/Plin'],
            ['nombre' => 'BCP'],
            ['nombre' => 'BBVA'],
            ['nombre' => 'INTERBANK'],
            ['nombre' => 'OTROS'],
        ];

        foreach ($mpagos as $mpago) {
            DB::table('metodo_pagos')->updateOrInsert(
                ['nombre' => $mpago['nombre']],
                []
            );
        }

        // Crear Comprobantes si no existen
        $comprobantes = [
            ['tipo_comprobante' => 'FACTURA'],
            ['tipo_comprobante' => 'BOLETA'],
            ['tipo_comprobante' => 'NOTA VENTA'],
        ];

        foreach ($comprobantes as $comprobante) {
            DB::table('comprobantes')->updateOrInsert(
                ['tipo_comprobante' => $comprobante['tipo_comprobante']],
                []
            );
        }

        // Asegurar que el comprobante con id=3 existe
        $comprobante = DB::table('comprobantes')->where('tipo_comprobante', 'NOTA VENTA')->first();

        if (!$comprobante) {
            $comprobanteId = DB::table('comprobantes')->insertGetId([
                'tipo_comprobante' => 'NOTA VENTA',
            ]);
        } else {
            $comprobanteId = $comprobante->id;
        }

        $proveedor = Proveedor::first();
        $cliente = Cliente::first();
        $metodoPago = MetodoPago::first();

        // Crear una Compra
        $compra = Compra::firstOrCreate([
            'numero_comprobante' => '212'
        ], [
            'fecha_hora' => now(),
            'impuesto' => 0.0,
            'total' => 50,
            'comprobante_id' => $comprobanteId, // Usamos el ID del comprobante
            'proveedore_id' => $proveedor->id,
        ]);

        // Asociar Productos a la Compra
        $productos = Producto::take(3)->get();

        foreach ($productos as $producto) {
            CompraProducto::firstOrCreate([
                'compra_id' => $compra->id,
                'producto_id' => $producto->id,
            ], [
                'cantidad' => 10,
                'precio_compra' => 0.5,
                'precio_venta' => 1.0,
            ]);
        }

        // Crear una Venta
        $venta = Venta::firstOrCreate([
            'serie' => '000',
            'numero_comprobante' => '212',
            'identificador_web' => 'C0d4r71c0212da',
            'estado'=> 0,
        ], [
            'comprobante_id' => 3,
            'user_id' => 1,
            'cliente_id' => $cliente->id,
            'metodo_pago_id' => $metodoPago->id,
            'fecha_hora' => now(),
            'total' => 1.0,
            'impuesto' => 0.0,
            'monto_recibido' => 5.0,
        ]);

        // Asociar Productos a la Venta
        foreach ($productos as $producto) {
            ProductoVenta::firstOrCreate([
                'venta_id' => $venta->id,
                'producto_id' => $producto->id,
            ], [
                'cantidad' => 1,
                'precio_ventas' => 1.0,
                'descuento' => 0.0,
            ]);
        }
    }
}
