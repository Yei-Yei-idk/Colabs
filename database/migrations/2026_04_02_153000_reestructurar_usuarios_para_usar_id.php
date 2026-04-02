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
        if (!Schema::hasTable('usuarios') || !Schema::hasColumn('usuarios', 'id') || !Schema::hasColumn('usuarios', 'user_id')) {
            return;
        }

        $this->dropForeignIfExists('reserva', 'reserva_ibfk_1');
        $this->dropForeignIfExists('calificaciones', 'calificaciones_ibfk_1');

        DB::statement('ALTER TABLE usuarios MODIFY id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE usuarios MODIFY id VARCHAR(255) NOT NULL');

        DB::statement("
            UPDATE usuarios
            SET id = CASE
                WHEN google_id IS NOT NULL AND google_id <> '' THEN google_id
                ELSE CONCAT('LOCAL_', user_id)
            END
        ");

        if ($this->primaryKeyColumn('usuarios') !== 'id') {
            DB::statement('ALTER TABLE usuarios DROP PRIMARY KEY');
            DB::statement('ALTER TABLE usuarios ADD PRIMARY KEY (id)');
        }

        if ($this->indexExists('usuarios', 'id')) {
            DB::statement('ALTER TABLE usuarios DROP INDEX `id`');
        }

        DB::statement('UPDATE reserva r INNER JOIN usuarios u ON r.user_id = u.user_id SET r.user_id = u.id');
        DB::statement('UPDATE calificaciones c INNER JOIN usuarios u ON c.user_id = u.user_id SET c.user_id = u.id');

        if (!$this->foreignExists('reserva', 'reserva_ibfk_1')) {
            DB::statement('ALTER TABLE reserva ADD CONSTRAINT reserva_ibfk_1 FOREIGN KEY (user_id) REFERENCES usuarios (id) ON UPDATE RESTRICT ON DELETE RESTRICT');
        }

        if (!$this->foreignExists('calificaciones', 'calificaciones_ibfk_1')) {
            DB::statement('ALTER TABLE calificaciones ADD CONSTRAINT calificaciones_ibfk_1 FOREIGN KEY (user_id) REFERENCES usuarios (id) ON UPDATE RESTRICT ON DELETE RESTRICT');
        }

        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }

    /**
     * Revierte la migracion.
     */
    public function down(): void
    {
        if (!Schema::hasTable('usuarios') || !Schema::hasColumn('usuarios', 'id')) {
            return;
        }

        $this->dropForeignIfExists('reserva', 'reserva_ibfk_1');
        $this->dropForeignIfExists('calificaciones', 'calificaciones_ibfk_1');

        if (!Schema::hasColumn('usuarios', 'user_id')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->string('user_id')->nullable()->after('id');
            });
        }

        DB::statement("
            UPDATE usuarios
            SET user_id = CASE
                WHEN id LIKE 'LOCAL_%' THEN SUBSTRING(id, 7)
                ELSE id
            END
        ");

        DB::statement('UPDATE reserva r INNER JOIN usuarios u ON r.user_id = u.id SET r.user_id = u.user_id');
        DB::statement('UPDATE calificaciones c INNER JOIN usuarios u ON c.user_id = u.id SET c.user_id = u.user_id');

        if ($this->primaryKeyColumn('usuarios') === 'id') {
            DB::statement('ALTER TABLE usuarios DROP PRIMARY KEY');
            DB::statement('ALTER TABLE usuarios ADD PRIMARY KEY (user_id)');
        }

        DB::statement('ALTER TABLE usuarios MODIFY user_id VARCHAR(255) NOT NULL');

        if (!$this->indexExists('usuarios', 'id')) {
            DB::statement('ALTER TABLE usuarios ADD UNIQUE INDEX `id` (`id`)');
        }

        if (!$this->foreignExists('reserva', 'reserva_ibfk_1')) {
            DB::statement('ALTER TABLE reserva ADD CONSTRAINT reserva_ibfk_1 FOREIGN KEY (user_id) REFERENCES usuarios (user_id) ON UPDATE RESTRICT ON DELETE RESTRICT');
        }

        if (!$this->foreignExists('calificaciones', 'calificaciones_ibfk_1')) {
            DB::statement('ALTER TABLE calificaciones ADD CONSTRAINT calificaciones_ibfk_1 FOREIGN KEY (user_id) REFERENCES usuarios (user_id) ON UPDATE RESTRICT ON DELETE RESTRICT');
        }
    }

    private function dropForeignIfExists(string $table, string $constraint): void
    {
        if ($this->foreignExists($table, $constraint)) {
            DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$constraint}");
        }
    }

    private function foreignExists(string $table, string $constraint): bool
    {
        $resultado = DB::selectOne(
            "SELECT COUNT(1) AS total FROM information_schema.table_constraints WHERE table_schema = DATABASE() AND table_name = ? AND constraint_name = ? AND constraint_type = 'FOREIGN KEY'",
            [$table, $constraint]
        );

        return ((int) ($resultado->total ?? 0)) > 0;
    }

    private function indexExists(string $table, string $index): bool
    {
        $resultado = DB::selectOne(
            "SELECT COUNT(1) AS total FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?",
            [$table, $index]
        );

        return ((int) ($resultado->total ?? 0)) > 0;
    }

    private function primaryKeyColumn(string $table): ?string
    {
        $resultado = DB::selectOne(
            "SELECT column_name FROM information_schema.key_column_usage WHERE table_schema = DATABASE() AND table_name = ? AND constraint_name = 'PRIMARY' LIMIT 1",
            [$table]
        );

        return $resultado->column_name ?? null;
    }
};
