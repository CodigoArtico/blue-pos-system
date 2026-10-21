<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();  // ID autoincrementable
            $table->dateTime('fecha');  // Fecha y hora del gasto
            $table->text('descripcion')->nullable();  // Descripción del gasto
            $table->decimal('monto', 10, 2);  // Monto del gasto
            $table->foreignId('metodo_pagos_id')->constrained('metodo_pagos')->onDelete('cascade');  // Relación con la tabla metodo_pagos
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');  // Relación con la tabla users
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();  // created_at y updated_at
        });
    }

    /**
     * Revertir la migración.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gastos');
    }
};
