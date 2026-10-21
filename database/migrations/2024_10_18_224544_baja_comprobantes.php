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
        Schema::create('baja_comprobantes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('emision_comprobante_id');
            $table->string('motivo')->nullable();
            $table->timestamp('fecha_baja')->nullable();
            $table->text('ticket_baja')->nullable();
            $table->string('estado_sunat')->nullable();
            $table->string('hash_cpe')->nullable();
            $table->timestamps();

            $table->foreign('emision_comprobante_id')->references('id')->on('emision_comprobantes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('baja_comprobantes');
    }
};
