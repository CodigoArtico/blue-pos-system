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
        Schema::create('ordenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_web_id')->constrained('users_web')->onDelete('cascade');
            $table->integer('estadoventa')->default(0); // Nuevo campo
            $table->string('codigo_orden'); // Codigo unico
            $table->string('identificador_web_venta')->nullable();
            $table->enum('estado', ['pendiente', 'procesando', 'completado', 'cancelado', 'online'])->default('pendiente');
            $table->decimal('total', 10, 2);
            $table->string('nombre'); // Campo para el nombre
            $table->string('telefono'); // Campo para el teléfono
            $table->string('direccion'); // Campo para la dirección
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes');
    }
};
