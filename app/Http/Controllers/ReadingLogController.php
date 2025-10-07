<?php

namespace App\Http\Controllers;

use App\Models\ReadingLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReadingLogController extends Controller
{
    /**
     * Listado de lecturas del usuario autenticado.
     */
    public function index()
    {
        // Ordeno por lo último guardado/actualizado
        $logs = ReadingLog::where('user_id', Auth::id())
            ->latest()
            ->paginate(12);

        return view('reading-logs.index', compact('logs'));
    }

    /**
     * Guarda/actualiza un log (se llama desde la ficha del libro).
     * Por ahora solo crea/actualiza con estado 'want' por defecto.
     */
    public function store(Request $request)
    {
        // Validación del payload que viene desde la vista de detalle
        $data = $request->validate([
            'volume_id'     => ['required', 'string'],
            'title'         => ['required', 'string'],
            'authors'       => ['nullable', 'string'],
            'thumbnail_url' => ['nullable', 'string'], // a veces vienen URLs raras
            'status'        => ['nullable', 'in:want,reading,read,dropped'],
        ]);

        $userId = Auth::id();
        $status = $data['status'] ?? 'want';

        // Trunco a 255 para evitar problemas con VARCHAR (comentario en español, estilo “yo”)
        $payload = [
            'title'         => Str::limit($data['title'], 255, ''),
            'authors'       => Str::limit($data['authors'] ?? '', 255, ''),
            'thumbnail_url' => Str::limit($data['thumbnail_url'] ?? '', 255, ''),
            'status'        => $status,
        ];

        try {
            ReadingLog::updateOrCreate(
                ['user_id' => $userId, 'volume_id' => $data['volume_id']],
                $payload
            );

            return redirect()
                ->route('books.show', $data['volume_id'])
                ->with('success', 'Libro guardado en tus lecturas.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('books.show', $data['volume_id'])
                ->with('error', 'No se pudo guardar el libro. ' . $e->getMessage());
        }
    }

    /**
     * Actualiza SOLO el estado del log (want/reading/read/dropped) desde el listado.
     */
    public function update(Request $request, ReadingLog $readingLog)
    {
        // Seguridad: solo el dueño del log puede modificarlo
        abort_unless($readingLog->user_id === Auth::id(), 403);

        $data = $request->validate([
            'status' => ['required', 'in:want,reading,read,dropped'],
        ]);

        $readingLog->update([
            'status' => $data['status'],
        ]);

        return back()->with('success', 'Estado actualizado.');
    }

    public function updateRating(\Illuminate\Http\Request $request, \App\Models\ReadingLog $readingLog)
    {
        // Solo el dueño del log puede modificarlo
        abort_unless($readingLog->user_id === \Illuminate\Support\Facades\Auth::id(), 403);

        $data = $request->validate([
            'rating' => ['nullable', 'integer', 'between:1,10'],
        ]);

        // Si no viene rating, lo limpiamos (null)
        $readingLog->update([
            'rating' => $data['rating'] ?? null,
        ]);

        return back()->with('success', 'Rating actualizado.');
    }
}
