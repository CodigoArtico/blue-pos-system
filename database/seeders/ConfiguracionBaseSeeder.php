<?php

namespace Database\Seeders;

use App\Models\Caracteristica;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Presentacion;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class ConfiguracionBaseSeeder extends Seeder
{
    public function run()
    {
        // Validar y crear Característica para Categoría
        $caracteristicaCategoria = Caracteristica::firstOrCreate([
            'nombre' => 'Variado'
        ], [
            'descripcion' => 'Descripción relacionada con categoria de Variado',
        ]);

        // Validar y crear Categoría
        Categoria::firstOrCreate([
            'caracteristica_id' => $caracteristicaCategoria->id,
        ]);

        // Validar y crear Característica para Marca
        $caracteristicaMarca = Caracteristica::firstOrCreate([
            'nombre' => 'Marca'
        ], [
            'descripcion' => 'Descripción relacionada con Marca',
        ]);

        // Validar y crear Marca
        $marca = Marca::firstOrCreate([
            'caracteristica_id' => $caracteristicaMarca->id,
        ]);

        // Validar y crear Característica para Presentación
        $caracteristicaPresentacion = Caracteristica::firstOrCreate([
            'nombre' => 'UND'
        ], [
            'descripcion' => 'Descripción relacionada con la presentación UND',
        ]);

        // Validar y crear Presentación
        $presentacion = Presentacion::firstOrCreate([
            'caracteristica_id' => $caracteristicaPresentacion->id,
        ]);

        // Validar y crear Producto
        $producto = Producto::firstOrCreate([
            'codigo' => 'P12345'
        ], [
            'nombre' => 'Pelota de Futbol',
            'descripcion' => 'Una pelota de futbol de alta calidad',
            'stock' => 100,
            'img_path' => 'productos/default.png',
            'marca_id' => $marca->id,
            'presentacione_id' => $presentacion->id,
        ]);

        // Asociar Producto con Categoría
        $producto->categorias()->syncWithoutDetaching([Categoria::first()->id]);
    }
}
