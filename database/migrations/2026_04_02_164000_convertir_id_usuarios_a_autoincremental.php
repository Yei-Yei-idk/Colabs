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
        if (!Schema::hasTable('usuarios') || !Schema::hasColumn('usuarios', 'id')) {
            return;
        }

        if ($this->esColumnaNumerica('usuarios', 'id')) {
            return;
        }

        $this->eliminarForaneaSiExiste('reserva', 'reserva_ibfk_1');
        $this->eliminarForaneaSiExiste('calificaciones', 'calificaciones_ibfk_1');

        if (!Schema::hasColumn('usuarios', 'id_nuevo')) {
            DB::statement('ALTER TABLE usuarios ADD COLUMN id_nuevo BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE FIRST');
        }

        if (Schema::hasTable('reserva') && Schema::hasColumn('reserva', 'user_id') && !Schema::hasColumn('reserva', 'user_id_nuevo')) {
            DB::statement('ALTER TABLE reserva ADD COLUMN user_id_nuevo BIGINT UNSIGNED NULL AFTER user_id');
            DB::statement('UPDATE reserva r INNER JOIN usuarios u ON r.user_id = u.id SET r.user_id_nuevo = u.id_nuevo');
            DB::statement('ALTER TABLE reserva DROP COLUMN user_id');
            DB::statement('ALTER TABLE reserva CHANGE user_id_nuevo user_id BIGINT UNSIGNED NOT NULL');
        }

        if (Schema::hasTable('calificaciones') && Schema::hasColumn('calificaciones', 'user_id') && !Schema::hasColumn('calificaciones', 'user_id_nuevo')) {
            DB::statement('ALTER TABLE calificaciones ADD COLUMN user_id_nuevo BIGINT UNSIGNED NULL AFTER user_id');
            DB::statement('UPDATE calificaciones c INNER JOIN usuarios u ON c.user_id = u.id SET c.user_id_nuevo = u.id_nuevo');
            DB::statement('ALTER TABLE calificaciones DROP COLUMN user_id');
            DB::statement('ALTER TABLE calificaciones CHANGE user_id_nuevo user_id BIGINT UNSIGNED NOT NULL');
        }

        DB::statement('ALTER TABLE usuarios DROP PRIMARY KEY');
        DB::statement('ALTER TABLE usuarios CHANGE id id_legacy VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE usuarios CHANGE id_nuevo id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        DB::statement('ALTER TABLE usuarios ADD PRIMARY KEY (id)');
        DB::statement('ALTER TABLE usuarios DROP COLUMN id_legacy');

        if ($this->indiceExiste('usuarios', 'id_nuevo')) {
            DB::statement('ALTER TABLE usuarios DROP INDEX id_nuevo');
        }

        if ($this->indiceExiste('reserva', 'user_id')) {
            DB::statement('ALTER TABLE reserva DROP INDEX user_id');
        }
        DB::statement('ALTER TABLE reserva ADD INDEX user_id (user_id)');

        if ($this->indiceExiste('calificaciones', 'user_id')) {
            DB::statement('ALTER TABLE calificaciones DROP INDEX user_id');
        }
        DB::statement('ALTER TABLE calificaciones ADD INDEX user_id (user_id)');

        if (Schema::hasTable('sessions') && Schema::hasColumn('sessions', 'user_id') && !$this->esColumnaNumerica('sessions', 'user_id')) {
            DB::statement("UPDATE sessions SET user_id = NULL WHERE user_id IS NOT NULL AND user_id NOT REGEXP '^[0-9]+$'");
            if ($this->indiceExiste('sessions', 'sessions_user_id_index')) {
                DB::statement('ALTER TABLE sessions DROP INDEX sessions_user_id_index');
            }
            DB::statement('ALTER TABLE sessions MODIFY user_id BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE sessions ADD INDEX sessions_user_id_index (user_id)');
        }

        DB::statement('ALTER TABLE reserva ADD CONSTRAINT reserva_ibfk_1 FOREIGN KEY (user_id) REFERENCES usuarios (id) ON UPDATE RESTRICT ON DELETE RESTRICT');
        DB::statement('ALTER TABLE calificaciones ADD CONSTRAINT calificaciones_ibfk_1 FOREIGN KEY (user_id) REFERENCES usuarios (id) ON UPDATE RESTRICT ON DELETE RESTRICT');
    }

    /**
     * Revierte la migracion.
     */
    public function down(): void
    {
        // Esta conversion se aplica para normalizar el identificador interno.
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
