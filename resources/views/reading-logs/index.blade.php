@extends('layouts.app')

@section('title', 'Mis lecturas')

@section('content')
<div class="container">
    <h1 class="mb-4">Mis lecturas</h1>

    {{-- Mensajes flash --}}
    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-warning mb-4">{{ session('error') }}</div>
    @endif

    @if($logs->isEmpty())
        <p>Todavía no has guardado ningún libro.</p>
        <a class="btn mt-2" href="{{ route('books.index') }}">Buscar libros</a>
    @else
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($logs as $log)
                <div class="card">
                    <a href="{{ route('books.show', $log->volume_id) }}" class="block">
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

                    {{-- Form para cambiar el estado del libro --}}
                    <div class="p-2 pt-0">
                        <form method="POST" action="{{ route('reading-logs.update', $log) }}" class="mt-2">
                            @csrf
                            @method('PATCH')
                            <label class="text-xs block mb-1">Cambiar estado:</label>
                            <div class="flex items-center gap-2">
                                <select name="status" class="input">
                                    <option value="want"    @selected($log->status === 'want')>want</option>
                                    <option value="reading" @selected($log->status === 'reading')>reading</option>
                                    <option value="read"    @selected($log->status === 'read')>read</option>
                                    <option value="dropped" @selected($log->status === 'dropped')>dropped</option>
                                </select>
                                <button class="btn">Actualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
