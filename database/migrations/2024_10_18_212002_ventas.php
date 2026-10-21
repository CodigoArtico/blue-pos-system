<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comprobante_id')->constrained('comprobantes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->onDelete('set null');
            $table->foreignId('metodo_pago_id')->nullable()->constrained('metodo_pagos')->onDelete('set null');
            $table->string('serie', 10)->nullable(false); // Asegura que no es nullable
            $table->string('correlativo')->nullable();
            $table->string('numero_comprobante', 255)->collate('utf8mb4_unicode_ci')->unique(); // UNIQUE
            $table->string('identificador_web')->nullable();
            $table->dateTime('fecha_hora')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->decimal('total', 10, 2)->unsigned();
            $table->decimal('impuesto', 8, 2)->unsigned();
            $table->decimal('monto_recibido', 10, 2)->nullable();
            $table->string('numero_operacion')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('ventas');
    }
};
