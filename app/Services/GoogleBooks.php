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

        $cacheKey = sprintf('gbooks.search.v2.%s.%d.%d', md5($q), $page, $perPage);

        return Cache::remember($cacheKey, now()->addMinutes(config('googlebooks.cache_minutes')), function () use ($q, $perPage, $page) {
            try {
                $base = config('googlebooks.base_url');
                $key  = config('googlebooks.key');
                $timeout = config('googlebooks.timeout');

                $responses = Http::pool(fn ($pool) => [
                    $pool->as('es')
                        ->baseUrl($base)->timeout($timeout)->acceptJson()
                        ->get('/volumes', ['q' => $q, 'maxResults' => 24, 'langRestrict' => 'es', 'key' => $key]),
                    $pool->as('en')
                        ->baseUrl($base)->timeout($timeout)->acceptJson()
                        ->get('/volumes', ['q' => $q, 'maxResults' => 24, 'langRestrict' => 'en', 'key' => $key]),
                ]);

                $itemsEs = (!$responses['es']->failed()) ? ($responses['es']->json()['items'] ?? []) : [];
                $itemsEn = (!$responses['en']->failed()) ? ($responses['en']->json()['items'] ?? []) : [];

                // Combinar: español primero
                $combined = array_merge($itemsEs, $itemsEn);

                // Filtrar sin portada
                $combined = array_values(array_filter($combined, function ($item) {
                    return !empty($item['volumeInfo']['imageLinks']);
                }));

                // Deduplicar por ISBN_13
                $seen = [];
                $deduped = [];
                foreach ($combined as $item) {
                    $isbn = null;
                    foreach ($item['volumeInfo']['industryIdentifiers'] ?? [] as $identifier) {
                        if ($identifier['type'] === 'ISBN_13') {
                            $isbn = $identifier['identifier'];
                            break;
                        }
                    }
                    $dedupeKey = $isbn ?? $item['id'] ?? null;
                    if ($dedupeKey === null || !isset($seen[$dedupeKey])) {
                        if ($dedupeKey !== null) $seen[$dedupeKey] = true;
                        $deduped[] = $item;
                    }
                }

                $total = count($deduped);
                $startIndex = ($page - 1) * $perPage;
                $pageItems = array_slice($deduped, $startIndex, $perPage);

                return [
                    'items'      => $pageItems,
                    'totalItems' => $total,
                    'page'       => $page,
                    'perPage'    => $perPage,
                    'error'      => null,
                ];
            } catch (\Throwable $e) {
                return [
                    'items'      => [],
                    'totalItems' => 0,
                    'page'       => $page,
                    'perPage'    => $perPage,
                    'error'      => 'Error de red: '.$e->getMessage(),
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
