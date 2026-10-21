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
        // Crear la tabla 'mesas'
        Schema::create('mesas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_mesa')->nullable(); // Código único de la mesa
            $table->string('nombre'); // Ejemplo: Mesa 1, Mesa 2, etc.
            $table->boolean('estado')->default(0); // 0 para libre, 1 para ocupada
            $table->boolean('reserva')->default(0); // 0 NO reservada, 1 reservada
            $table->string('nombre_reserva')->nullable();
            $table->timestamp('hora_reserva')->nullable();
            $table->timestamps();
        });

        // Crear la tabla 'mesa_producto'
        Schema::create('mesa_producto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesa_id')->constrained('mesas')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->integer('cantidad');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mesa_producto');
        Schema::dropIfExists('mesas');
    }
};
