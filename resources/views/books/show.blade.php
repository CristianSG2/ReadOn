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
@endphp

<div class="container">
    {{-- Mensajes flash --}}
    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-warning mb-4">{{ session('error') }}</div>
    @endif

    <a href="{{ route('books.index') }}" class="text-sm" style="color:#7aa2ff;">&larr; Volver a la búsqueda</a>

    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Portada grande --}}
        <div>
            <div class="card-thumb aspect-[3/4] bg-gray-100 overflow-hidden">
                @if($thumb)
                    <img src="{{ $thumb }}" alt="{{ $title }}">
                @else
                    <div class="thumb-placeholder">Sin portada</div>
                @endif
            </div>
        </div>

        {{-- Info --}}
        <div class="md:col-span-2">
            <h1 class="text-2xl font-bold">{{ $title }}</h1>
            <p class="muted">de {{ $authors }}</p>

            <div class="mt-2 text-sm muted">
                <span>Publicado: {{ $published }}</span>
                @if($pages) · <span>{{ $pages }} páginas</span>@endif
                @if($cats) · <span>{{ $cats }}</span>@endif
                @if($avg) · <span>⭐ {{ $avg }}/5</span>@endif
            </div>

            <div class="prose prose-sm mt-4 max-w-none">
                {!! $desc ?? '<em>Sin descripción.</em>' !!}
            </div>

            {{-- Guardar en mis lecturas --}}
            <div class="mt-6">
                @auth
                    <form method="POST" action="{{ route('reading-logs.store') }}">
                        @csrf
                        {{-- En este micro-paso guardo con estado "want" por defecto --}}
                        <input type="hidden" name="volume_id" value="{{ $volumeId }}">
                        <input type="hidden" name="title" value="{{ $title }}">
                        <input type="hidden" name="authors" value="{{ $authors === 'Autor desconocido' ? '' : $authors }}">
                        <input type="hidden" name="thumbnail_url" value="{{ $thumb }}">
                        <input type="hidden" name="status" value="want">

                        <button class="btn">Guardar en mis lecturas</button>
                    </form>
                @else
                    <a class="btn" href="{{ route('login') }}">Inicia sesión para guardar</a>
                @endauth
            </div>

            @if($error ?? false)
                <div class="alert alert-warning mt-4">{{ $error }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
