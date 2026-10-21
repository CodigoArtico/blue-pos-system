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
        // TABLA EMPRESA (Datos del Emisor)
        Schema::create('empresa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('ruc_dni', 11)->unique();
            $table->string('razon_social', 255)->nullable();
            $table->string('direccion_empresa', 255)->nullable();
            $table->string('ubigeo', 80)->nullable();
            $table->string('departamento', 80)->nullable();
            $table->string('provincia', 80)->nullable();
            $table->string('distrito', 80)->nullable();
            $table->string('img_account', 255)->nullable();
            $table->string('razon_social_comprobante', 255)->nullable();
            $table->string('telefono_comprobante', 80)->nullable();
            $table->string('direccion_comprobante', 80)->nullable();
            $table->string('subtitulo_principal_comprobante', 80)->nullable();
            $table->text('descripcion_aviso_comprobante', 255)->nullable();
            $table->string('despedida_comprobante', 255)->nullable();
            $table->foreignId('certificado_id')->constrained('certificados')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('empresa');
    }
};
