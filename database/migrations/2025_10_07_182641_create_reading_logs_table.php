<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tabla para guardar el progreso del usuario con cada libro
    public function up(): void
    {
        Schema::create('reading_logs', function (Blueprint $table) {
            $table->id();

            // Relación con usuarios (si se borra el usuario, se borran sus logs)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // ID del volumen en Google Books (p. ej. "zyTCAlFPjgYC")
            $table->string('volume_id');

            // Metadatos mínimos para mostrar sin reconsultar la API
            $table->string('title');
            $table->string('authors')->nullable();        // "Autor 1, Autor 2"
            $table->string('thumbnail_url')->nullable();  // URL de portada pequeña

            // Estado de lectura (lo validaremos en app): want|reading|read|dropped
            $table->string('status', 10)->default('want')->index();

            // Valoración opcional 1–10
            $table->unsignedTinyInteger('rating')->nullable();

            // Reseña libre opcional
            $table->text('review')->nullable();

            $table->timestamps();

            // Evitar duplicados del mismo libro para el mismo usuario
            $table->unique(['user_id', 'volume_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reading_logs');
    }
};
