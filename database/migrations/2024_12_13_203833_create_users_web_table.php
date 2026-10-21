<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users_web', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->text('img')->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->timestamps();
        });

        // Insertar un dato ficticio
        DB::table('users_web')->insert([
            'id'=> 87970,
            'name' => 'Publico General',
            'email' => 'juan.perez@example.com',
            'password' => bcrypt('12345678'), // Asegúrate de encriptar la contraseña
            'direccion' => 'Calle Falsa 123',
            'telefono' => '1234567890',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_web');
    }
};
