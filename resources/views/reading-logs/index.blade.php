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
        {{-- Grid de tarjetas --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($logs as $log)
                @php
                    // Mejora de calidad para portadas de Google Books (usa zoom alto si viene mini)
                    $upgrade = $upgrade ?? function (?string $url, int $zoom = 4) {
                        if (!$url) return $url;
                        if (str_contains($url, 'zoom=')) {
                            return preg_replace('/zoom=\d+/', 'zoom='.$zoom, $url);
                        }
                        if (str_contains($url, 'books.google')) {
                            return $url.(str_contains($url, '?') ? '&' : '?').'zoom='.$zoom;
                        }
                        return $url;
                    };
                    $cover = $upgrade($log->thumbnail_url, 4);
                @endphp

                <div class="card">
                    {{-- Portada --}}
                    <div class="card-thumb aspect-[3/4] bg-gray-100 overflow-hidden">
                        @if($cover)
                            <img src="{{ $cover }}" alt="{{ $log->title }}">
                        @else
                            <div class="thumb-placeholder">Sin portada</div>
                        @endif
                    </div>

                    {{-- Contenido --}}
                    <div class="card-body">
                        <a class="block" href="{{ route('books.show', $log->volume_id) }}">
                            <h3 class="title line-clamp-2">{{ $log->title }}</h3>
                        </a>
                        @if($log->authors)
                            <p class="muted line-clamp-1">{{ $log->authors }}</p>
                        @endif

                        <p class="meta">
                            Estado: <span class="badge">{{ $log->status }}</span>
                            @if(!is_null($log->rating)) · ⭐ {{ $log->rating }}/10 @endif
                        </p>

                        {{-- Cambiar estado --}}
                        <form method="POST" action="{{ route('reading-logs.update', $log) }}" class="mt-2">
                            @csrf
                            @method('PATCH')
                            <label class="label">Cambiar estado:</label>
                            <div class="status-row">
                                <select name="status" class="input">
                                    <option value="want"    @selected($log->status === 'want')>want</option>
                                    <option value="reading" @selected($log->status === 'reading')>reading</option>
                                    <option value="read"    @selected($log->status === 'read')>read</option>
                                    <option value="dropped" @selected($log->status === 'dropped')>dropped</option>
                                </select>
                                <button class="btn">Actualizar</button>
                            </div>
                        </form>

                        {{-- Rating (5 estrellas con medias → envia 1..10) --}}
                        <form method="POST" action="{{ route('reading-logs.rating', $log) }}" class="mt-3">
                            @csrf
                            @method('PATCH')
                            <label class="label">Rating</label>

                            <div class="stars" data-initial="{{ (int)($log->rating ?? 0) }}">
                                <input type="hidden" name="rating" value="{{ (int)($log->rating ?? 0) }}">
                                <div class="stars__display" aria-hidden="true">★★★★★</div>
                                <div class="stars__fill" style="--p: {{ (($log->rating ?? 0) * 10) }}%;" aria-hidden="true">★★★★★</div>
                                <div class="stars__hit">
                                    @for($i = 1; $i <= 10; $i++)
                                        <button type="button" data-v="{{ $i }}" aria-label="{{ number_format($i/2,1) }} estrellas"></button>
                                    @endfor
                                </div>
                            </div>

                            <div class="form-actions mt-2">
                                <button class="btn">Guardar rating</button>
                                <button class="btn btn-outline" name="rating" value="">Quitar rating</button>
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

{{-- Script del widget de estrellas (una sola vez en la página) --}}
<script>
document.querySelectorAll('.stars').forEach(stars => {
  const input = stars.querySelector('input[name="rating"]');
  const fill  = stars.querySelector('.stars__fill');
  const hit   = stars.querySelector('.stars__hit');
  let current = parseInt(input.value || 0, 10) || 0;

  const set = v => {
    current = v || 0;
    input.value = current || '';
    fill.style.setProperty('--p', (current * 10) + '%');
  };

  hit.addEventListener('mousemove', e => {
    const v = parseInt(e.target.dataset.v || 0, 10);
    if (v) fill.style.setProperty('--p', (v * 10) + '%');
  });
  hit.addEventListener('mouseleave', () => {
    fill.style.setProperty('--p', (current * 10) + '%');
  });
  hit.addEventListener('click', e => {
    const v = parseInt(e.target.dataset.v || 0, 10);
    if (v) set(v);
  });
});
</script>
@endsection
