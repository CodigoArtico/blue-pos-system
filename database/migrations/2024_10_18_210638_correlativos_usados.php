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
        Schema::create('correlativos_usados', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT
            $table->enum('tipo_comprobante', ['factura', 'boleta', 'nota_credito', 'nota_debito']); // ENUM
            $table->string('serie', 10); // VARCHAR(10)
            $table->unsignedBigInteger('correlativo'); // BIGINT UNSIGNED
            $table->timestamp('fecha')->default(now()); // TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            
            $table->unique(['tipo_comprobante', 'serie', 'correlativo'], 'correlativo_unique'); // UNIQUE KEY
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('correlativos_usados');
    }
};
