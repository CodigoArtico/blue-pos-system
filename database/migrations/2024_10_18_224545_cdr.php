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
        Schema::create('cdr', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emision_comprobante_id')->constrained('emision_comprobantes')->onDelete('cascade');
            $table->enum('estado', ['aceptada', 'rechazada', 'observada']);
            $table->string('codigo_respuesta', 10);
            $table->text('mensaje')->nullable();
            $table->string('archivo_cdr', 255)->nullable();
            $table->string('archivo_zip', 255)->nullable();
            $table->text('contenido_xml')->nullable();
            $table->timestamp('fecha_respuesta')->default(now());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('cdr');
    }
};
