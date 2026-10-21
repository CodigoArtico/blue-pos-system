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
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social', 255)->nullable();
            $table->string('tipo_documento', 5)->nullable();
            $table->string('numero_documento', 11)->nullable();
            $table->string('estado_sunat', 255)->nullable();
            $table->string('condicion', 255)->nullable();
            $table->string('departamento', 255)->nullable();
            $table->string('direccion', 255);
            $table->string('distrito', 255)->nullable();
            $table->string('provincia', 255)->nullable();
            $table->string('ubigeo', 255)->nullable();
            $table->string('via_nombre', 255)->nullable();
            $table->string('via_tipo', 255)->nullable();
            $table->string('zona_tipo', 255)->nullable();
            $table->string('tipo_persona', 255);
            $table->tinyInteger('estado')->default(1);
            $table->foreignId('documento_id')->constrained('documentos')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('personas');
    }
};
