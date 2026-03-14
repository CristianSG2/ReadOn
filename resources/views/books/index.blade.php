@extends('layouts.app')

@section('title', 'Buscar libros')

@section('content')
<div class="container">
    <h1 class="mb-3">Buscar libros</h1>

    {{-- Buscador simple (GET) --}}
    <form method="GET" action="{{ route('books.index') }}" class="mb-4 book-search">
        <div class="book-search__row">
            <input
                type="text"
                name="q"
                value="{{ old('q', $q ?? '') }}"
                placeholder="Título, autor, ISBN…"
                class="input"
                autofocus
            />
            <button class="btn">Buscar</button>
        </div>
        @error('q')
            <p class="text-error">{{ $message }}</p>
        @enderror
    </form>

    @if(empty($q) && empty($results))
        <div class="search-empty">
            <p class="search-empty__title">¿Qué vas a leer hoy?</p>
            <p class="search-empty__sub">Busca por título, autor o ISBN</p>
            <div class="search-chips" id="search-chips">
                @foreach(['Brandon Sanderson', 'Dune', 'Sapiens', 'Tolkien', 'Haruki Murakami'] as $suggestion)
                    <button type="button" class="search-chip" data-q="{{ $suggestion }}">{{ $suggestion }}</button>
                @endforeach
            </div>
        </div>
        <script>
        document.getElementById('search-chips').addEventListener('click', function(e) {
            const chip = e.target.closest('.search-chip');
            if (!chip) return;
            const form = document.querySelector('.book-search');
            form.querySelector('input[name=q]').value = chip.dataset.q;
            form.submit();
        });
        </script>
    @endif

    @if(!empty($results))
        @php
            $items = $results['items'] ?? [];
            $total = (int)($results['totalItems'] ?? 0);
            $page  = (int)($results['page'] ?? 1);
            $per   = (int)($results['perPage'] ?? 12);
            $hasPrev = $page > 1;
            $hasNext = $total > ($page * $per);

            // Mejorar calidad de portada
            $upgrade = function (?string $url, int $zoom = 3) {
                if (!$url) return $url;
                if (str_contains($url, 'zoom=')) {
                    return preg_replace('/zoom=\d+/', 'zoom='.$zoom, $url);
                }
                if (str_contains($url, 'books.google')) {
                    return $url.(str_contains($url, '?') ? '&' : '?').'zoom='.$zoom;
                }
                return $url;
            };
        @endphp

        <p class="mb-2 text-sm">
            Resultados: {{ number_format($total, 0, ',', '.') }} @if($total>0) — página {{ $page }} @endif
        </p>

        {{-- GRID COMPACTO (auto-fill) --}}
        <div class="results-grid">
            @forelse($items as $it)
                @php
                    $v = $it['volumeInfo'] ?? [];
                    $id = $it['id'] ?? null;
                    $title = $v['title'] ?? 'Sin título';
                    $authors = isset($v['authors']) ? implode(', ', $v['authors']) : null;

                    $imgs = $v['imageLinks'] ?? [];
                    $thumb = $imgs['extraLarge'] ?? $imgs['large'] ?? $imgs['medium']
                          ?? $imgs['small'] ?? $imgs['thumbnail'] ?? $imgs['smallThumbnail'] ?? null;
                    $thumb = $thumb ? str_replace('http://', 'https://', $thumb) : null;
                    $thumb = $upgrade($thumb, 3);

                    // Fallback Open Library si Google Books no devuelve portada
                    if (!$thumb) {
                        $identifiers = $v['industryIdentifiers'] ?? [];
                        $isbn = null;
                        foreach ($identifiers as $_id) {
                            if (($_id['type'] ?? '') === 'ISBN_13') { $isbn = $_id['identifier']; break; }
                        }
                        if (!$isbn) {
                            foreach ($identifiers as $_id) {
                                if (($_id['type'] ?? '') === 'ISBN_10') { $isbn = $_id['identifier']; break; }
                            }
                        }
                        if ($isbn) {
                            $thumb = "https://covers.openlibrary.org/b/isbn/{$isbn}-L.jpg?default=false";
                        }
                    }
                @endphp

                <a href="{{ $id ? route('books.show', $id) : '#' }}" class="card block">
                    <div class="card-thumb aspect-[3/4] bg-gray-100 overflow-hidden">
                        @if($thumb)
                            <img src="{{ $thumb }}" alt="{{ $title }}"
                                 onerror="this.onerror=null;this.src='{{ asset('images/no-cover.svg') }}'">
                        @else
                            <div class="thumb-placeholder">Sin portada</div>
                        @endif
                    </div>
                    <div class="card-body">
                        <h3 class="title line-clamp-2">{{ $title }}</h3>
                        @if($authors)
                            <p class="muted line-clamp-1">{{ $authors }}</p>
                        @endif
                    </div>
                </a>
            @empty
                <p>No hay resultados para “{{ $q }}”.</p>
            @endforelse
        </div>

        {{-- Paginación básica --}}
        @if($total > 0)
            <div class="mt-4 form-actions">
                @if($hasPrev)
                    <a class="btn btn-outline" href="{{ route('books.index', ['q'=>$q, 'page'=>$page-1]) }}">⬅️ Anterior</a>
                @endif
                @if($hasNext)
                    <a class="btn" href="{{ route('books.index', ['q'=>$q, 'page'=>$page+1]) }}">Siguiente ➡️</a>
                @endif
            </div>
        @endif
    @endif
</div>
@endsection
