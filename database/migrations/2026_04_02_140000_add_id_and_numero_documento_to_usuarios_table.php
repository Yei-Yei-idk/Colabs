<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migracion.
     */
    public function up(): void
    {
        if (!Schema::hasTable('usuarios')) {
            return;
        }

        if (!Schema::hasColumn('usuarios', 'id')) {
            DB::statement('ALTER TABLE usuarios ADD COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE FIRST');
        }

        if (!Schema::hasColumn('usuarios', 'numero_documento')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->string('numero_documento')->nullable()->after('user_id');
            });
        }

        DB::statement("UPDATE usuarios SET numero_documento = user_id WHERE numero_documento IS NULL AND user_id REGEXP '^[0-9]+$'");

        if (!$this->existeIndice('usuarios', 'usuarios_numero_documento_unique')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->unique('numero_documento', 'usuarios_numero_documento_unique');
            });
        }
    }

    /**
     * Revierte la migracion.
     */
    public function down(): void
    {
        if (!Schema::hasTable('usuarios')) {
            return;
        }

        if ($this->existeIndice('usuarios', 'usuarios_numero_documento_unique')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropUnique('usuarios_numero_documento_unique');
            });
        }

        if (Schema::hasColumn('usuarios', 'numero_documento')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropColumn('numero_documento');
            });
        }

        if (Schema::hasColumn('usuarios', 'id')) {
            DB::statement('ALTER TABLE usuarios DROP COLUMN id');
        }
    }

    private function existeIndice(string $tabla, string $indice): bool
    {
        $resultado = DB::selectOne(
            "SELECT COUNT(1) AS total FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?",
            [$tabla, $indice]
        );

        return ((int) ($resultado->total ?? 0)) > 0;
    }
};
