<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('sessions')) {
            return;
        }

        DB::statement('ALTER TABLE sessions DROP INDEX sessions_user_id_index');
        DB::statement('ALTER TABLE sessions MODIFY user_id VARCHAR(255) NULL');
        DB::statement('ALTER TABLE sessions ADD INDEX sessions_user_id_index (user_id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('sessions')) {
            return;
        }

        DB::statement('ALTER TABLE sessions DROP INDEX sessions_user_id_index');
        DB::statement('ALTER TABLE sessions MODIFY user_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE sessions ADD INDEX sessions_user_id_index (user_id)');
    }
};
