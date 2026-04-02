<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     */
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('google_id')->nullable()->unique()->after('user_correo');
            $table->string('avatar')->nullable()->after('google_id');
        });

        DB::statement('ALTER TABLE usuarios MODIFY user_contrasena VARCHAR(255) NULL');
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE usuarios MODIFY user_contrasena VARCHAR(255) NOT NULL');

        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropUnique('usuarios_google_id_unique');
            $table->dropColumn(['google_id', 'avatar']);
        });
    }
};
