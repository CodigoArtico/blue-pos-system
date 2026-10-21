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
        Schema::create('emision_comprobantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comprobante_id')->constrained('comprobantes')->onDelete('cascade');
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->string('serie', 10);
            $table->unsignedBigInteger('correlativo');
            $table->dateTime('fecha_emision');
            $table->string('hash_cpe', 64)->nullable();
            $table->string('estado_sunat')->nullable();
            $table->text('firma_digital')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('emision_comprobantes');
    }
};
