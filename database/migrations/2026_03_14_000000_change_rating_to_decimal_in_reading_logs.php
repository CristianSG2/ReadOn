<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Convertir valores existentes 1–10 a escala 0.5–5.0 antes de cambiar el tipo
        DB::statement('UPDATE reading_logs SET rating = rating / 2.0 WHERE rating IS NOT NULL');

        Schema::table('reading_logs', function (Blueprint $table) {
            $table->decimal('rating', 3, 1)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Revertir a entero 1–10
        DB::statement('UPDATE reading_logs SET rating = ROUND(rating * 2) WHERE rating IS NOT NULL');

        Schema::table('reading_logs', function (Blueprint $table) {
            $table->unsignedTinyInteger('rating')->nullable()->change();
        });
    }
};
