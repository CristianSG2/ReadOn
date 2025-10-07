@extends('layouts.app')

@section('title', 'Mis lecturas')

@section('content')
<div class="container">
    <h1 class="mb-4">Mis lecturas</h1>

    @if($logs->isEmpty())
        <p>Todavía no has guardado ningún libro.</p>
        <a class="btn mt-2" href="{{ route('books.index') }}">Buscar libros</a>
    @else
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($logs as $log)
                <a href="{{ route('books.show', $log->volume_id) }}" class="card block">
                    <div class="aspect-[3/4] bg-gray-100 flex items-center justify-center overflow-hidden">
                        @if($log->thumbnail_url)
                            <img src="{{ $log->thumbnail_url }}" alt="{{ $log->title }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-xs text-gray-500">Sin portada</span>
                        @endif
                    </div>
                    <div class="p-2">
                        <h3 class="font-semibold text-sm line-clamp-2">{{ $log->title }}</h3>
                        @if($log->authors)
                            <p class="text-xs text-gray-600 line-clamp-1">{{ $log->authors }}</p>
                        @endif
                        <p class="text-xs mt-1">
                            Estado: <span class="font-medium">{{ $log->status }}</span>
                            @if(!is_null($log->rating)) · ⭐ {{ $log->rating }}/10 @endif
                        </p>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
