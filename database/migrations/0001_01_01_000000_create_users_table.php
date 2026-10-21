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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT
            $table->string('name'); // VARCHAR(255)
            $table->string('email')->unique(); // VARCHAR(255) UNIQUE
            $table->string('img')->nullable(); // VARCHAR(255) NULL
            $table->boolean('estado')->default(1); // BOOLEAN DEFAULT 1
            $table->timestamp('email_verified_at')->nullable(); // TIMESTAMP NULL
            $table->string('password'); // VARCHAR(255)
            $table->string('remember_token', 100)->nullable(); // VARCHAR(100) NULL
            $table->timestamps(); // Crea columnas created_at y updated_at
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
