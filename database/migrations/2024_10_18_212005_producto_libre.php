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
        Schema::create('productos_libres', function (Blueprint $table) {
            $table->id(); // Asumiendo que quieres un ID auto-incremental en lugar de un int(11) simple
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->string('nombre')->nullable();
            $table->integer('cantidad')->nullable();
            $table->decimal('precio_venta', 10, 2)->nullable();
            $table->decimal('descuento', 10, 2)->nullable();
            $table->timestamps(0); // Usar `timestamps(0)` para ajustar el formato de timestamp a `CURRENT_TIMESTAMP` por defecto
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos_libres');
    }
};
