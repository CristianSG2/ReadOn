@extends('layouts.app')

@section('title', $book['volumeInfo']['title'] ?? 'Detalle del libro')

@section('content')
@php
    $v = $book['volumeInfo'] ?? [];
    $title = $v['title'] ?? 'Sin título';
    $authors = isset($v['authors']) ? implode(', ', $v['authors']) : 'Autor desconocido';
    $thumb = $v['imageLinks']['thumbnail'] ?? null;
    $desc = $v['description'] ?? null; // a veces viene con HTML
    $published = $v['publishedDate'] ?? '—';
    $pages = $v['pageCount'] ?? null;
    $cats = isset($v['categories']) ? implode(' · ', $v['categories']) : null;
    $avg = $v['averageRating'] ?? null;
@endphp

<div class="container">
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

            {{-- Hook para el siguiente bloque (Logs de lectura) --}}
            <div class="mt-6">
                <form method="POST" action="#" onsubmit="return false;">
                    @csrf
                    <button class="btn" disabled title="Se activará en el siguiente bloque">
                        Guardar en mis lecturas
                    </button>
                </form>
            </div>

            @if($error)
                <div class="alert alert-warning mt-4">{{ $error }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
