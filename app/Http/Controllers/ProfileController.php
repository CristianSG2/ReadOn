<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ReadingLog;

class ProfileController extends Controller
{
    /**
     * Muestra el perfil del usuario autenticado con estadísticas de lectura.
     */
    public function index()
    {
        $userId = Auth::id();

        // Conteos por estado (wishlist, reading, read, dropped)
        $counts = ReadingLog::where('user_id', $userId)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $wishlist = (int) ($counts['wishlist'] ?? 0);
        $reading  = (int) ($counts['reading'] ?? 0);
        $read     = (int) ($counts['read'] ?? 0);
        $dropped  = (int) ($counts['dropped'] ?? 0);

        // Total general
        $total = $wishlist + $reading + $read + $dropped;

        // Media de rating (1 decimal). Si no hay, devolver null
        $avgRating = ReadingLog::where('user_id', $userId)
            ->whereNotNull('rating')
            ->avg('rating');
        $avgRating = $avgRating !== null ? number_format((float) $avgRating, 1) : null;

        // Últimos 5 registros creados por el usuario (orden descendente)
        $recentLogs = ReadingLog::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'title', 'status', 'rating', 'created_at']);

        return view('profile.index', [
            'total'     => $total,
            'wishlist'  => $wishlist,
            'reading'   => $reading,
            'read'      => $read,
            'dropped'   => $dropped,
            'avgRating' => $avgRating,
            'recent'    => $recentLogs,
        ]);
    }
}
