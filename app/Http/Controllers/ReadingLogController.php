<?php

namespace App\Http\Controllers;

use App\Models\ReadingLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReadingLogController extends Controller
{
    // Guarda/actualiza un log de lectura para el usuario autenticado
    public function store(Request $request)
    {
        // Validación básica del payload que viene desde la vista de detalle
        $data = $request->validate([
            'volume_id'     => ['required', 'string'],
            'title'         => ['required', 'string'],
            'authors'       => ['nullable', 'string'],
            'thumbnail_url' => ['nullable', 'string'], // no usar 'url' porque a veces vienen URLs raras con params
            'status'        => ['nullable', 'in:want,reading,read,dropped'],
        ]);

        $userId = Auth::id();
        $status = $data['status'] ?? 'want';

        // Truncar a 255 para evitar problemas con VARCHAR
        $payload = [
            'title'         => Str::limit($data['title'], 255, ''),
            'authors'       => Str::limit($data['authors'] ?? '', 255, ''),
            'thumbnail_url' => Str::limit($data['thumbnail_url'] ?? '', 255, ''),
            'status'        => $status,
        ];

        try {
            // Si ya existe, actualizar metadata/estado; si no, lo crea.
            ReadingLog::updateOrCreate(
                ['user_id' => $userId, 'volume_id' => $data['volume_id']],
                $payload
            );

            return redirect()
                ->route('books.show', $data['volume_id'])
                ->with('success', 'Libro guardado en tus lecturas.');
        } catch (\Throwable $e) {
            // Control básico de errores (DB, carreras, etc.)
            return redirect()
                ->route('books.show', $data['volume_id'])
                ->with('error', 'No se pudo guardar el libro. '.$e->getMessage());
        }
    }
}
