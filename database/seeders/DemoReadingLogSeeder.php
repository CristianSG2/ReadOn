<?php

namespace Database\Seeders;

use App\Models\ReadingLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class DemoReadingLogSeeder extends Seeder
{
    public function run(): void
    {
        if (empty(config('googlebooks.key'))) {
            $this->command->warn('GOOGLE_BOOKS_API_KEY no configurada en .env. Saltando DemoReadingLogSeeder.');
            return;
        }

        $user = User::where('email', 'demo@readon.app')->first();

        if (!$user) {
            $this->command->error('Usuario demo@readon.app no encontrado. Ejecuta UserSeeder primero.');
            return;
        }

        $books = [
            [
                'query'  => 'El Imperio Final Sanderson',
                'status' => 'read',
                'rating' => 5.0,
                'review' => 'Un sistema de magia increíblemente original. Sanderson construye un mundo oscuro y opresivo donde cada detalle importa. La evolución de Vin es magistral.',
            ],
            [
                'query'  => 'El Camino de los Reyes Sanderson',
                'status' => 'read',
                'rating' => 4.5,
                'review' => 'Ambicioso y épico. Los personajes de Kaladin y Dalinar son fascinantes. Requiere paciencia al principio pero la recompensa es enorme.',
            ],
            [
                'query'  => 'It Stephen King',
                'status' => 'read',
                'rating' => 4.0,
                'review' => null,
            ],
            [
                'query'  => 'El Resplandor Stephen King',
                'status' => 'dropped',
                'rating' => 3.0,
                'review' => null,
            ],
            [
                'query'  => 'Asesinato Orient Express Christie',
                'status' => 'read',
                'rating' => 5.0,
                'review' => 'Un clásico perfecto. El giro final sigue siendo uno de los mejores de la historia del género. Poirot en su mejor momento.',
            ],
            [
                'query'  => '1984 Orwell',
                'status' => 'reading',
                'rating' => null,
                'review' => null,
            ],
            [
                'query'  => 'Rebelión en la Granja Orwell',
                'status' => 'wishlist',
                'rating' => null,
                'review' => null,
            ],
            [
                'query'  => 'Norwegian Wood Murakami',
                'status' => 'read',
                'rating' => 4.5,
                'review' => 'Melancólica y hermosa. Murakami captura la pérdida y el paso a la madurez con una sensibilidad única. La banda sonora es perfecta.',
            ],
            [
                'query'  => 'Kafka en la Orilla Murakami',
                'status' => 'wishlist',
                'rating' => null,
                'review' => null,
            ],
        ];

        $created = 0;
        $skipped = 0;
        $failed  = 0;

        foreach ($books as $book) {
            $volume = $this->fetchFirstVolume($book['query']);

            if (!$volume) {
                $this->command->warn("  ✗ Sin resultado para: {$book['query']}");
                $failed++;
                continue;
            }

            $volumeId = $volume['id'];
            $info     = $volume['volumeInfo'] ?? [];

            $thumb = $info['imageLinks']['thumbnail']
                  ?? $info['imageLinks']['smallThumbnail']
                  ?? null;
            $thumb = $thumb ? str_replace('http://', 'https://', $thumb) : null;

            // ISBN_13 preferido, fallback ISBN_10
            $isbn = null;
            foreach ($info['industryIdentifiers'] ?? [] as $id) {
                if ($id['type'] === 'ISBN_13') { $isbn = $id['identifier']; break; }
            }
            if (!$isbn) {
                foreach ($info['industryIdentifiers'] ?? [] as $id) {
                    if ($id['type'] === 'ISBN_10') { $isbn = $id['identifier']; break; }
                }
            }

            if (ReadingLog::where('user_id', $user->id)->where('volume_id', $volumeId)->exists()) {
                $this->command->line("  ~ Ya existe: {$info['title']}");
                $skipped++;
                continue;
            }

            ReadingLog::create([
                'user_id'       => $user->id,
                'volume_id'     => $volumeId,
                'title'         => $info['title'] ?? $book['query'],
                'authors'       => isset($info['authors']) ? implode(', ', $info['authors']) : null,
                'thumbnail_url' => $thumb,
                'isbn'          => $isbn,
                'status'        => $book['status'],
                'rating'        => $book['rating'],
                'review'        => $book['review'],
            ]);

            $this->command->info("  ✓ {$info['title']} ({$volumeId})");
            $created++;
        }

        $this->command->info("DemoReadingLogSeeder: {$created} creadas, {$skipped} ya existían, {$failed} sin resultado.");
    }

    private function fetchFirstVolume(string $query): ?array
    {
        try {
            $response = Http::baseUrl(config('googlebooks.base_url'))
                ->timeout((float) config('googlebooks.timeout'))
                ->acceptJson()
                ->get('/volumes', [
                    'q'          => $query,
                    'maxResults' => 1,
                    'key'        => config('googlebooks.key'),
                ]);

            if ($response->failed()) {
                return null;
            }

            $items = $response->json('items');

            return $items[0] ?? null;
        } catch (\Throwable) {
            return null;
        }
    }
}
