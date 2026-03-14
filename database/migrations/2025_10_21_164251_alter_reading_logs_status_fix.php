<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Cambiar valores antiguos 'want' → 'wishlist'
        DB::statement("UPDATE reading_logs SET status = 'wishlist' WHERE status = 'want'");

        // Limpieza previa: null o vacío → wishlist
        DB::statement("UPDATE reading_logs SET status = 'wishlist' WHERE status IS NULL OR status = ''");

        // Sentencias exclusivas de PostgreSQL (no compatibles con SQLite)
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE reading_logs ALTER COLUMN status SET DEFAULT 'wishlist'");

            try {
                DB::statement("ALTER TABLE reading_logs DROP CONSTRAINT IF EXISTS chk_reading_logs_status");
            } catch (\Throwable $e) {}

            DB::statement("
                ALTER TABLE reading_logs
                ADD CONSTRAINT chk_reading_logs_status
                CHECK (status IN ('wishlist','reading','read','dropped'))
            ");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            try {
                DB::statement("ALTER TABLE reading_logs DROP CONSTRAINT IF EXISTS chk_reading_logs_status");
            } catch (\Throwable $e) {}

            DB::statement("ALTER TABLE reading_logs ALTER COLUMN status SET DEFAULT 'want'");
        }

        DB::statement("UPDATE reading_logs SET status = 'want' WHERE status = 'wishlist'");
    }
};
