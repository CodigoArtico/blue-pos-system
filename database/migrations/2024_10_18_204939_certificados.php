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
        Schema::create('certificados', function (Blueprint $table) {
            $table->id();
            $table->string('ruc')->nullable();
            $table->string('certificado');
            $table->string('usuario_sunat'); // Campo para el usuario de SUNAT
            $table->string('password_sunat'); // Campo para la contraseña de SUNAT
            $table->string('client_id_sire', 255)->nullable();
            $table->string('client_secret_sire', 255)->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('certificados');
    }
};
