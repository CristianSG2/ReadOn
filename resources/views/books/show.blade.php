@extends('layouts.app')

@section('title', $book['volumeInfo']['title'] ?? 'Detalle del libro')

@section('content')
@php
    $v = $book['volumeInfo'] ?? [];
    $title = $v['title'] ?? 'Sin título';
    $authors = isset($v['authors']) ? implode(', ', $v['authors']) : 'Autor desconocido';
    $desc = $v['description'] ?? null;
    $published = $v['publishedDate'] ?? '—';
    $pages = $v['pageCount'] ?? null;
    $cats = isset($v['categories']) ? implode(' · ', $v['categories']) : null;
    $avg = $v['averageRating'] ?? null;
    $volumeId = $book['id'] ?? null;

    // Portada: mejor calidad posible
    $imgs = $v['imageLinks'] ?? [];
    $thumb = $imgs['extraLarge'] ?? $imgs['large'] ?? $imgs['medium']
          ?? $imgs['small'] ?? $imgs['thumbnail'] ?? $imgs['smallThumbnail'] ?? null;
    $thumb = $thumb ? str_replace('http://', 'https://', $thumb) : null;

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
    $thumb = $upgrade($thumb, 3);

    // ISBN para fallback de portada (preferimos ISBN_13, aceptamos ISBN_10)
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
    if (!$thumb && $isbn) {
        $thumb = "https://covers.openlibrary.org/b/isbn/{$isbn}-L.jpg?default=false";
    }
@endphp

<div class="container">
    @if(session('success'))
        <script>showToast('{{ session('success') }}');</script>
    @endif
    @if(session('error'))
        <script>showToast('{{ session('error') }}', 'error');</script>
    @endif

    <a href="{{ route('books.index') }}" class="back-link">&larr; Volver a la búsqueda</a>

    <div class="book-detail-grid">
        {{-- Portada grande --}}
        <div>
            <div class="card-thumb card-thumb--contain aspect-[2/3]">
                @if($thumb)
                    <img src="{{ $thumb }}" alt="{{ $title }}"
                         onerror="this.onerror=null;this.src='{{ asset('images/no-cover.svg') }}'">
                @else
                    <div class="thumb-placeholder">Sin portada</div>
                @endif
            </div>
        </div>

        {{-- Info --}}
        <div>
            <h1>{{ $title }}</h1>
            <p class="muted">de {{ $authors }}</p>
            {{-- Guardar en mis lecturas --}}
            <div class="mt-6">
                @auth
                    <form id="save-book-form" method="POST" action="{{ route('reading-logs.store') }}">
                        @csrf
                        {{-- En este micro-paso guardo con estado "want" por defecto --}}
                        <input type="hidden" name="volume_id" value="{{ $volumeId }}">
                        <input type="hidden" name="title" value="{{ $title }}">
                        <input type="hidden" name="authors" value="{{ $authors === 'Autor desconocido' ? '' : $authors }}">
                        <input type="hidden" name="thumbnail_url" value="{{ $thumb }}">
                        <input type="hidden" name="isbn" value="{{ $isbn }}">
                        <input type="hidden" name="status" value="wishlist">

                        <button class="btn" id="save-book-btn">Guardar en mis lecturas</button>
                    </form>
                    <script>
                    (function () {
                        var form = document.getElementById('save-book-form');
                        var btn  = document.getElementById('save-book-btn');
                        if (!form) return;
                        form.addEventListener('submit', function (e) {
                            e.preventDefault();
                            var inputs = form.querySelectorAll('input[name]');
                            var body   = {};
                            inputs.forEach(function (el) { body[el.name] = el.value; });
                            var csrf = (form.querySelector('input[name="_token"]') || {}).value || '';
                            fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrf,
                                },
                                body: JSON.stringify(body),
                            })
                            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
                            .then(function (res) {
                                showToast(res.data.message, res.ok ? 'success' : 'error');
                                if (res.ok) {
                                    btn.disabled = true;
                                    btn.textContent = 'Guardado en tus lecturas';
                                }
                            })
                            .catch(function () { showToast('Error al guardar', 'error'); });
                        });
                    })();
                    </script>
                @else
                    <a class="btn" href="{{ route('login') }}">Inicia sesión para guardar</a>
                @endauth
            </div>
            <div class="mt-2 text-sm muted">
                <span>Publicado: {{ $published }}</span>
                @if($pages) · <span>{{ $pages }} páginas</span>@endif
                @if($cats) · <span>{{ $cats }}</span>@endif
                @if($avg) · <span>⭐ {{ $avg }}/5</span>@endif
            </div>

            <div class="book-prose mt-4">
                {!! $desc ?? '<em>Sin descripción.</em>' !!}
            </div>



            @if($error ?? false)
                <script>showToast('{{ $error }}', 'error');</script>
            @endif
        </div>
    </div>
</div>
@endsection
