<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 80);
            $table->string('descripcion', 255)->nullable();
            $table->integer('stock')->default(0);
            $table->date('fecha_vencimiento')->nullable();
            $table->string('img_path', 255)->nullable();
            $table->string('img_url', 255)->nullable();
            $table->string('medida', 255)->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->tinyInteger('destacado')->default(0);
            $table->foreignId('marca_id')->constrained('marcas')->onDelete('cascade');
            $table->foreignId('presentacione_id')->constrained('presentaciones')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('productos');
    }
};
