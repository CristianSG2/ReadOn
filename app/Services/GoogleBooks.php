<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GoogleBooks
{
    /**
     * Búsqueda de libros.
     * - q: término de búsqueda
     * - page: página 1-n (internamente se traduce a startIndex)
     * - perPage: resultados por página (máx. 40 soporta Google)
     *
     * Devuelve array con: items, totalItems, page, perPage, error|null
     */
    public function search(string $q, int $page = 1, int $perPage = 12): array
    {
        $q = trim($q);
        $page = max(1, $page);
        $perPage = max(1, min(40, $perPage)); // Google limita a 40

        if ($q === '') {
            return ['items' => [], 'totalItems' => 0, 'page' => $page, 'perPage' => $perPage, 'error' => null];
        }

        $startIndex = ($page - 1) * $perPage;
        $cacheKey = sprintf('gbooks.search.%s.%d.%d', md5($q), $page, $perPage);

        return Cache::remember($cacheKey, now()->addMinutes(config('googlebooks.cache_minutes')), function () use ($q, $perPage, $startIndex, $page) {
            try {
                $response = Http::baseUrl(config('googlebooks.base_url'))
                    ->timeout(config('googlebooks.timeout'))
                    ->acceptJson()
                    ->get('/volumes', [
                        'q'          => $q,
                        'maxResults' => $perPage,
                        'startIndex' => $startIndex,
                        'key'        => config('googlebooks.key'),
                        // Se puede añadir: 'orderBy' => 'relevance|newest', 'printType' => 'books'
                    ]);

                if ($response->failed()) {
                    return [
                        'items' => [],
                        'totalItems' => 0,
                        'page' => $page,
                        'perPage' => $perPage,
                        'error' => 'La API respondió con error (HTTP '.$response->status().').',
                    ];
                }

                $json = $response->json();

                return [
                    'items'      => $json['items'] ?? [],
                    'totalItems' => $json['totalItems'] ?? 0,
                    'page'       => $page,
                    'perPage'    => $perPage,
                    'error'      => null,
                ];
            } catch (\Throwable $e) {
                // Control básico de errores de red/timeout
                return [
                    'items' => [],
                    'totalItems' => 0,
                    'page' => $page,
                    'perPage' => $perPage,
                    'error' => 'Error de red: '.$e->getMessage(),
                ];
            }
        });
    }

    /**
     * Detalle de un volumen por ID.
     * Devuelve array con: item|null y error|null
     */
    public function getVolume(string $id): array
    {
        $id = trim($id);
        if ($id === '') {
            return ['item' => null, 'error' => 'ID vacío'];
        }

        $cacheKey = 'gbooks.volume.'.md5($id);

        return Cache::remember($cacheKey, now()->addMinutes(config('googlebooks.cache_minutes')), function () use ($id) {
            try {
                $response = Http::baseUrl(config('googlebooks.base_url'))
                    ->timeout(config('googlebooks.timeout'))
                    ->acceptJson()
                    ->get('/volumes/'.$id, [
                        'key' => config('googlebooks.key'),
                    ]);

                if ($response->failed()) {
                    return ['item' => null, 'error' => 'La API respondió con error (HTTP '.$response->status().').'];
                }

                return ['item' => $response->json(), 'error' => null];
            } catch (\Throwable $e) {
                return ['item' => null, 'error' => 'Error de red: '.$e->getMessage()];
            }
        });
    }
}
