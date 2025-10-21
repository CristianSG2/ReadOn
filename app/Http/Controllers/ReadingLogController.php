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
        $logs = ReadingLog::where('user_id', Auth::id())
            ->latest()
            ->paginate(12);

        return view('reading-logs.index', compact('logs'));
    }

    /**
     * Guarda o actualiza un registro desde la ficha del libro.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'volume_id'     => ['required', 'string'],
            'title'         => ['required', 'string'],
            'authors'       => ['nullable', 'string'],
            'thumbnail_url' => ['nullable', 'string'],
            'status'        => ['nullable', 'in:wishlist,reading,read,dropped'],
        ]);

        $userId = Auth::id();
        $status = $data['status'] ?? 'wishlist';

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
     * Actualiza el estado (wishlist/reading/read/dropped) desde el listado.
     */
    public function update(Request $request, ReadingLog $readingLog)
    {
        abort_unless($readingLog->user_id === Auth::id(), 403);

        $data = $request->validate([
            'status' => ['required', 'in:wishlist,reading,read,dropped'],
        ]);

        $readingLog->update([
            'status' => $data['status'],
        ]);

        return back()->with('success', 'Estado actualizado.');
    }

    /**
     * Actualiza el rating (1–10 o vacío para limpiar).
     */
    public function updateRating(Request $request, ReadingLog $readingLog)
    {
        abort_unless($readingLog->user_id === Auth::id(), 403);

        $data = $request->validate([
            'rating' => ['nullable', 'integer', 'between:1,10'],
        ]);

        $readingLog->update([
            'rating' => $data['rating'] ?? null,
        ]);

        return back()->with('success', 'Rating actualizado.');
    }

    /**
     * Actualiza o elimina la reseña (texto).
     */
    public function updateReview(Request $request, ReadingLog $readingLog)
    {
        abort_unless($readingLog->user_id === Auth::id(), 403);

        $data = $request->validate([
            'review' => ['nullable', 'string', 'max:1000'],
        ]);

        $review = trim((string)($data['review'] ?? ''));

        $readingLog->update([
            'review' => $review !== '' ? $review : null,
        ]);

        return back()->with('success', $review !== '' ? 'Reseña actualizada.' : 'Reseña eliminada.');
    }

    /**
     * Elimina un registro.
     */
    public function destroy(Request $request, ReadingLog $readingLog)
    {
        abort_unless($readingLog->user_id === Auth::id(), 403);

        try {
            $readingLog->delete();

            return redirect()
                ->route('reading-logs.index')
                ->with('success', 'Registro eliminado correctamente.');
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('reading-logs.index')
                ->with('error', 'No se pudo eliminar el registro. Inténtalo de nuevo.');
        }
    }
}
