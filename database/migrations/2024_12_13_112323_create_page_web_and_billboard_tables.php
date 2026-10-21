<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Uso de DB en lugar de los modelos

class CreatePageWebAndBillboardTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Verificar si la tabla page_web ya existe
        if (!Schema::hasTable('page_web')) {
            Schema::create('page_web', function (Blueprint $table) {
                $table->id();
                $table->string('url_sistem')->nullable();
                $table->string('url_web')->nullable();
                $table->string('telefono')->nullable();
                $table->string('nombre');
                $table->text('descripcion')->nullable();
                $table->timestamps();
            });
        }

        // Verificar si la tabla billboards ya existe
        if (!Schema::hasTable('billboards')) {
            Schema::create('billboards', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_page')->constrained('page_web')->onDelete('cascade');
                $table->tinyInteger('estado_url')->default(1);
                $table->string('img_url');
                $table->tinyInteger('estado_path')->default(1);
                $table->string('img_path')->nullable();
                $table->timestamps();
            });
        }

        // Inserción de una página en page_web usando DB::table
        $pageWebId = DB::table('page_web')->insertGetId([
            'url_sistem' => '',
            'url_web' => 'https://tu-dominio-web.com',
            'nombre' => 'Cod-Art',
            'descripcion' => 'Página dedicada a la promoción de imágenes de alta calidad.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // URLs de imágenes para billboards
        $img_urls = [
            "https://img.freepik.com/psd-gratis/diseno-especial-medios-sociales-cafe_505751-6239.jpg",
            "https://img.freepik.com/psd-premium/pastel-cafe-negro-maqueta-pizarra_23-2148363734.jpg",
            "https://img.freepik.com/psd-gratis/banner-web-promocional-venta-menu-cafe-especial-o-plantilla-banner-instagram_505751-3236.jpg",
            "https://img.freepik.com/psd-gratis/plantilla-poster-empaquetado-galletas-diseno-plano_23-2149385579.jpg",
            "https://img.freepik.com/psd-premium/mockup-flat-lay-marco-espacio-trabajo_23-2148199670.jpg",
            "https://img.freepik.com/psd-gratis/plantilla-volante-cuadrado-cafeteria_23-2148448469.jpg",
            "https://img.freepik.com/psd-gratis/promocion-menu-bebidas-cafeteria-redes-sociales-diseno-plantilla-banner-publicacion-instagram_84443-996.jpg",
            "https://img.freepik.com/psd-premium/comida-deliciosa-angulo-alto-mesa_23-2149687659.jpg",
            "https://img.freepik.com/psd-gratis/plantilla-poster-cafeteria_23-2148715683.jpg",
            "https://img.freepik.com/psd-gratis/plantilla-poster-cafeteria_23-2148715683.jpg",
        ];

        // Insertar las imágenes en billboards asociadas a la página web usando DB::table
        foreach ($img_urls as $url) {
            DB::table('billboards')->insert([
                'id_page' => $pageWebId, // Asocia las imágenes con la página web creada
                'img_url' => $url,
                'img_path' => '', // Puedes dejar img_path vacío si no se guarda un archivo
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Eliminar tabla billboards primero por las relaciones
        Schema::dropIfExists('billboards');
        // Luego eliminar la tabla page_web
        Schema::dropIfExists('page_web');
    }
}
