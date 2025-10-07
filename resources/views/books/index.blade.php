@extends('layouts.app')

@section('title', 'Buscar libros')

@section('content')
<div class="container">
    <h1 class="mb-3">Buscar libros</h1>

    {{-- Buscador simple (GET) --}}
    <form method="GET" action="{{ route('books.index') }}" class="mb-4">
        <div class="flex gap-2">
            <input
                type="text"
                name="q"
                value="{{ old('q', $q) }}"
                placeholder="Título, autor, ISBN…"
                class="input"
                autofocus
            />
            <button class="btn">Buscar</button>
        </div>
        @error('q')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </form>

    @if($results)
        @if($results['error'])
            <div class="alert alert-warning mb-4">
                {{ $results['error'] }}
            </div>
        @endif

        @php
            $items = $results['items'] ?? [];
            $total = (int)($results['totalItems'] ?? 0);
            $page  = (int)($results['page'] ?? 1);
            $per   = (int)($results['perPage'] ?? 12);
            $hasPrev = $page > 1;
            $hasNext = $total > ($page * $per);
        @endphp

        <p class="mb-2 text-sm">
            Resultados: {{ $total }} @if($total>0) — página {{ $page }} @endif
        </p>

        {{-- Grid simple de cards --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse($items as $it)
                @php
                    $v = $it['volumeInfo'] ?? [];
                    $id = $it['id'] ?? null;
                    $title = $v['title'] ?? 'Sin título';
                    $authors = isset($v['authors']) ? implode(', ', $v['authors']) : 'Autor desconocido';
                    $thumb = $v['imageLinks']['thumbnail'] ?? null;
                @endphp
                <a href="{{ $id ? route('books.show', $id) : '#' }}" class="card block">
                    <div class="aspect-[3/4] bg-gray-100 flex items-center justify-center overflow-hidden">
                        @if($thumb)
                            <img src="{{ $thumb }}" alt="{{ $title }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-xs text-gray-500">Sin portada</span>
                        @endif
                    </div>
                    <div class="p-2">
                        <h3 class="font-semibold text-sm line-clamp-2">{{ $title }}</h3>
                        <p class="text-xs text-gray-600 line-clamp-1">{{ $authors }}</p>
                    </div>
                </a>
            @empty
                <p>No hay resultados para “{{ $q }}”.</p>
            @endforelse
        </div>

        {{-- Paginación muy básica --}}
        @if($total > 0)
            <div class="flex items-center gap-2 mt-4">
                @if($hasPrev)
                    <a class="btn" href="{{ route('books.index', ['q'=>$q, 'page'=>$page-1]) }}">⬅️ Anterior</a>
                @endif
                @if($hasNext)
                    <a class="btn" href="{{ route('books.index', ['q'=>$q, 'page'=>$page+1]) }}">Siguiente ➡️</a>
                @endif
            </div>
        @endif
    @endif
</div>
@endsection
