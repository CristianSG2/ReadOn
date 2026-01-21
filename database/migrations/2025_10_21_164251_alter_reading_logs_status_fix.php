<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Cambiar valores antiguos 'want' → 'wishlist'
        DB::statement("UPDATE reading_logs SET status = 'wishlist' WHERE status = 'want'");

        // Default nuevo
        DB::statement("ALTER TABLE reading_logs ALTER COLUMN status SET DEFAULT 'wishlist'");

        // Limpieza previa: null o vacío → wishlist
        DB::statement("UPDATE reading_logs SET status = 'wishlist' WHERE status IS NULL OR status = ''");

        // Borrar constraint anterior si existía
        try {
            DB::statement("ALTER TABLE reading_logs DROP CONSTRAINT IF EXISTS chk_reading_logs_status");
        } catch (\Throwable $e) {}

        // Añadir constraint CHECK con nuevos valores
        DB::statement("
            ALTER TABLE reading_logs
            ADD CONSTRAINT chk_reading_logs_status
            CHECK (status IN ('wishlist','reading','read','dropped'))
        ");
    }

    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE reading_logs DROP CONSTRAINT IF EXISTS chk_reading_logs_status");
        } catch (\Throwable $e) {}

        DB::statement("ALTER TABLE reading_logs ALTER COLUMN status SET DEFAULT 'want'");
        DB::statement("UPDATE reading_logs SET status = 'want' WHERE status = 'wishlist'");
    }
};
