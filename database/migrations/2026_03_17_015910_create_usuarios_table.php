<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('numero_documento')->nullable()->unique('usuarios_numero_documento_unique');
            $table->string('user_nombre');
            $table->string('user_correo')->unique();
            $table->string('google_id')->nullable()->unique();
            $table->string('avatar')->nullable();
            $table->bigInteger('user_telefono');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('verification_token')->nullable();
            $table->timestamp('verification_token_expires_at')->nullable();
            $table->string('user_contrasena')->nullable();
            $table->integer('rol_id')->index('rol_id');
            $table->rememberToken();

            $table->foreign('rol_id', 'rol_id')
                ->references('rol_id')
                ->on('rol')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
