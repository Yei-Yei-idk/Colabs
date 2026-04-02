<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migracion.
     */
    public function up(): void
    {
        $this->normalizarIndiceUsuarios();
        $this->normalizarSessions();
        $this->normalizarRelacionUsuario('reserva', 'reserva_ibfk_1');
        $this->normalizarRelacionUsuario('calificaciones', 'calificaciones_ibfk_1');
    }

    /**
     * Revierte la migracion.
     */
    public function down(): void
    {
        // Esta migracion solo normaliza el estado final del esquema.
    }

    private function normalizarIndiceUsuarios(): void
    {
        if (!Schema::hasTable('usuarios')) {
            return;
        }

        if ($this->indiceExiste('usuarios', 'id_nuevo')) {
            DB::statement('ALTER TABLE usuarios DROP INDEX id_nuevo');
        }
    }

    private function normalizarSessions(): void
    {
        if (!Schema::hasTable('sessions') || !Schema::hasColumn('sessions', 'user_id')) {
            return;
        }

        if ($this->esColumnaNumerica('sessions', 'user_id')) {
            if (!$this->indiceExiste('sessions', 'sessions_user_id_index')) {
                DB::statement('ALTER TABLE sessions ADD INDEX sessions_user_id_index (user_id)');
            }
            return;
        }

        DB::statement("UPDATE sessions SET user_id = NULL WHERE user_id IS NOT NULL AND user_id NOT REGEXP '^[0-9]+$'");

        if ($this->indiceExiste('sessions', 'sessions_user_id_index')) {
            DB::statement('ALTER TABLE sessions DROP INDEX sessions_user_id_index');
        }

        DB::statement('ALTER TABLE sessions MODIFY user_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE sessions ADD INDEX sessions_user_id_index (user_id)');
    }

    private function normalizarRelacionUsuario(string $tabla, string $foranea): void
    {
        if (!Schema::hasTable($tabla) || !Schema::hasColumn($tabla, 'user_id')) {
            return;
        }

        if (!$this->esColumnaNumerica($tabla, 'user_id')) {
            $this->eliminarForaneaSiExiste($tabla, $foranea);

            if ($this->indiceExiste($tabla, 'user_id')) {
                DB::statement("ALTER TABLE {$tabla} DROP INDEX user_id");
            }

            DB::statement("ALTER TABLE {$tabla} MODIFY user_id BIGINT UNSIGNED NOT NULL");
            DB::statement("ALTER TABLE {$tabla} ADD INDEX user_id (user_id)");
        }

        if (!$this->foraneaExiste($tabla, $foranea)) {
            DB::statement("ALTER TABLE {$tabla} ADD CONSTRAINT {$foranea} FOREIGN KEY (user_id) REFERENCES usuarios (id) ON UPDATE RESTRICT ON DELETE RESTRICT");
        }
    }

    private function eliminarForaneaSiExiste(string $tabla, string $foranea): void
    {
        if ($this->foraneaExiste($tabla, $foranea)) {
            DB::statement("ALTER TABLE {$tabla} DROP FOREIGN KEY {$foranea}");
        }
    }

    private function foraneaExiste(string $tabla, string $foranea): bool
    {
        $resultado = DB::selectOne(
            "SELECT COUNT(1) AS total
             FROM information_schema.table_constraints
             WHERE table_schema = DATABASE()
               AND table_name = ?
               AND constraint_name = ?
               AND constraint_type = 'FOREIGN KEY'",
            [$tabla, $foranea]
        );

        return ((int) ($resultado->total ?? 0)) > 0;
    }

    private function indiceExiste(string $tabla, string $indice): bool
    {
        $resultado = DB::selectOne(
            "SELECT COUNT(1) AS total
             FROM information_schema.statistics
             WHERE table_schema = DATABASE()
               AND table_name = ?
               AND index_name = ?",
            [$tabla, $indice]
        );

        return ((int) ($resultado->total ?? 0)) > 0;
    }

    private function esColumnaNumerica(string $tabla, string $columna): bool
    {
        $resultado = DB::selectOne(
            "SELECT data_type AS tipo
             FROM information_schema.columns
             WHERE table_schema = DATABASE()
               AND table_name = ?
               AND column_name = ?",
            [$tabla, $columna]
        );

        $tipo = strtolower((string) ($resultado->tipo ?? ''));
        return in_array($tipo, ['bigint', 'int', 'integer', 'smallint', 'mediumint', 'tinyint'], true);
    }
};
