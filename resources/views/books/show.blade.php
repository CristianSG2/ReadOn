@extends('layouts.app')

@section('title', $book['volumeInfo']['title'] ?? 'Detalle del libro')

@section('content')
@php
    $v = $book['volumeInfo'] ?? [];
    $title = $v['title'] ?? 'Sin título';
    $authors = isset($v['authors']) ? implode(', ', $v['authors']) : 'Autor desconocido';
    $thumb = $v['imageLinks']['thumbnail'] ?? null;
    $desc = $v['description'] ?? null;
    $published = $v['publishedDate'] ?? '—';
    $pages = $v['pageCount'] ?? null;
    $cats = isset($v['categories']) ? implode(' · ', $v['categories']) : null;
    $avg = $v['averageRating'] ?? null;
    $volumeId = $book['id'] ?? null;
@endphp

<div class="container">
    {{-- Mensajes flash --}}
    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-warning mb-4">{{ session('error') }}</div>
    @endif

    <a href="{{ route('books.index') }}" class="text-sm text-blue-600">&larr; Volver a la búsqueda</a>

    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <div class="bg-gray-100 aspect-[3/4] flex items-center justify-center overflow-hidden">
                @if($thumb)
                    <img src="{{ $thumb }}" alt="{{ $title }}" class="w-full h-full object-cover">
                @else
                    <span class="text-xs text-gray-500">Sin portada</span>
                @endif
            </div>
        </div>
        <div class="md:col-span-2">
            <h1 class="text-2xl font-bold">{{ $title }}</h1>
            <p class="text-gray-700">de {{ $authors }}</p>

            <div class="mt-2 text-sm text-gray-600">
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
                        {{-- Guardar con estado "want" por defecto --}}
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

            @if($error)
                <div class="alert alert-warning mt-4">{{ $error }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
