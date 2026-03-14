<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reading_logs', function (Blueprint $table) {
            $table->string('isbn', 20)->nullable()->after('thumbnail_url');
        });
    }

    public function down(): void
    {
        Schema::table('reading_logs', function (Blueprint $table) {
            $table->dropColumn('isbn');
        });
    }
};
