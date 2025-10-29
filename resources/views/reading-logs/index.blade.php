@extends('layouts.app')

@section('title', 'Mis lecturas')

@section('content')
<div class="container">
    <h1 class="mb-4">Mis lecturas</h1>

    {{-- Mensajes flash (mantengo estos tal cual) --}}
    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-warning mb-4">{{ session('error') }}</div>
    @endif

    {{-- Empty state cuando no hay registros --}}
    @if($logs->isEmpty())
        <div class="empty-state">
            <div class="empty-state__icon" aria-hidden="true">📚</div>
            <h2 class="empty-state__title">Aún no hay lecturas</h2>
            <p class="empty-state__text">Busco un libro y creo mi primer registro para empezar a llevar el control.</p>
            <a class="btn empty-state__cta" href="{{ route('books.index') }}">Buscar libros</a>
        </div>
    @else
        {{-- Grid de tarjetas de lectura --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($logs as $log)
                @php
                    // Subo la calidad de la miniatura si viene de Google Books
                    $upgrade = $upgrade ?? function (?string $url, int $zoom = 4) {
                        if (!$url) return $url;
                        if (str_contains($url, 'zoom=')) return preg_replace('/zoom=\d+/', 'zoom='.$zoom, $url);
                        if (str_contains($url, 'books.google')) return $url.(str_contains($url, '?') ? '&' : '?').'zoom='.$zoom;
                        return $url;
                    };
                    $cover = $upgrade($log->thumbnail_url, 4);

                    // Mapeo estado → clase visual del badge
                    $badgeClass = match($log->status) {
                        'wishlist' => 'badge badge--wishlist',
                        'reading'  => 'badge badge--reading',
                        'read'     => 'badge badge--read',
                        'dropped'  => 'badge badge--dropped',
                        default    => 'badge badge--wishlist',
                    };
                @endphp

                <div class="card">
                    {{-- Miniatura + overlay de borrar --}}
                    <div class="card-thumb aspect-[3/4] bg-gray-100 overflow-hidden relative">
                        @if($cover)
                            <img src="{{ $cover }}" alt="{{ $log->title }}">
                        @else
                            <div class="thumb-placeholder">Sin portada</div>
                        @endif

                        {{-- Botón de borrar (confirmación simple por ahora) --}}
                        <form
                            action="{{ route('reading-logs.destroy', $log) }}"
                            method="POST"
                            class="thumb-actions"
                            onsubmit="return confirm('¿Seguro que quiero eliminar este registro?');"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon btn-danger" aria-label="Eliminar registro" title="Eliminar registro">
                                &times;
                            </button>
                        </form>
                    </div>

                    {{-- Cuerpo de la tarjeta --}}
                    <div class="card-body">
                        <a class="block" href="{{ route('books.show', $log->volume_id) }}">
                            <h3 class="title line-clamp-2">{{ $log->title }}</h3>
                        </a>
                        @if($log->authors)
                            <p class="muted line-clamp-1">{{ $log->authors }}</p>
                        @endif

                        {{-- Badge con label en español (viene del accessor status_label del modelo) --}}
                        <p class="meta">
                            Estado:
                            <span class="{{ $badgeClass }}">{{ $log->status_label }}</span>
                            @if(!is_null($log->rating)) · ⭐ {{ $log->rating }}/10 @endif
                        </p>

                        {{-- Selector de estado (labels en español, values en slugs ingleses) --}}
                        <form method="POST" action="{{ route('reading-logs.update', $log) }}" class="mt-2">
                            @csrf
                            @method('PATCH')
                            <label class="label">Estado</label>
                            <div class="form-row">
                                <select class="input" name="status" required>
                                    <option value="wishlist" @selected($log->status === 'wishlist')>Lista de deseos</option>
                                    <option value="reading"  @selected($log->status === 'reading')>Leyendo</option>
                                    <option value="read"     @selected($log->status === 'read')>Leído</option>
                                    <option value="dropped"  @selected($log->status === 'dropped')>Abandonado</option>
                                </select>
                                <button class="btn">Actualizar</button>
                            </div>
                        </form>

                        {{-- Bloque de rating (10 pasos, media estrella cada paso) --}}
                        <form method="POST" action="{{ route('reading-logs.rating', $log) }}"" class="mt-3">
                            @csrf
                            @method('PATCH')
                            <label class="label">Rating</label>

                            <div class="stars" data-initial="{{ (int)($log->rating ?? 0) }}">
                                <input type="hidden" name="rating" value="{{ (int)($log->rating ?? 0) }}">
                                <div class="stars__display" aria-hidden="true">★★★★★</div>
                                <div class="stars__fill" style="width: {{ (($log->rating ?? 0) * 10) }}%;" aria-hidden="true">★★★★★</div>
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

                        {{-- Reseña (muestro snippet y dejo el form plegable) --}}
                        @if (!empty($log->review))
                            @php($snippet = \Illuminate\Support\Str::limit($log->review, 140))
                            <div class="review-snippet mt-2">
                                <strong>Reseña:</strong> {{ $snippet }}@if (mb_strlen($log->review) > 140)<span class="muted">…</span>@endif
                            </div>
                        @endif

                        {{-- Toggle de reseña (simple, sin dependencias) --}}
                        <button
                            type="button"
                            class="btn btn-secondary review-toggle mt-2"
                            data-target="#review-form-{{ $log->id }}"
                            aria-expanded="false"
                            aria-controls="review-form-{{ $log->id }}"
                        >
                            {{ !empty($log->review) ? 'Editar reseña' : 'Añadir reseña' }}
                        </button>

                        <div id="review-form-{{ $log->id }}" class="review-form" hidden>
                            <form
                                action="{{ route('reading-logs.review', ['readingLog' => $log->id]) }}"
                                method="POST"
                                class="review-form__inner"
                            >
                                @csrf
                                @method('PATCH')

                                <label for="review-{{ $log->id }}" class="review-form__label">
                                    Escribo mi reseña (máx. 1000 caracteres):
                                </label>
                                <textarea
                                    id="review-{{ $log->id }}"
                                    name="review"
                                    maxlength="1000"
                                    class="review-form__textarea"
                                    rows="5"
                                    placeholder="¿Qué me ha parecido este libro?"
                                >{{ old('review', $log->review) }}</textarea>

                                <div class="review-form__actions">
                                    <button type="submit" class="btn">Guardar</button>
                                    <button
                                        type="button"
                                        class="btn btn-link danger"
                                        onclick="if(confirm('¿Eliminar la reseña de este registro?')) { const f = this.closest('form'); f.querySelector('textarea[name=review]').value = ''; f.submit(); }"
                                    >Eliminar reseña</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Paginación estándar --}}
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    @endif
</div>

{{-- JS mínimo para rating y toggle de reseña (mantengo el patrón que ya vengo usando) --}}
<script>
// Rating interactivo
document.querySelectorAll('.stars').forEach(stars => {
  const input = stars.querySelector('input[name=rating]');
  const fill  = stars.querySelector('.stars__fill');
  const hit   = stars.querySelector('.stars__hit');
  let current = parseInt(input.value || 0, 10) || 0;

  const set = v => {
    current = v || 0;
    input.value = current || '';
    fill.style.width = (current * 10) + '%';
  };

  hit.addEventListener('mousemove', e => {
    const v = parseInt(e.target.dataset.v || 0, 10);
    if (v) fill.style.width = (v * 10) + '%';
  });
  hit.addEventListener('mouseleave', () => {
    fill.style.width = (current * 10) + '%';
  });
  hit.addEventListener('click', e => {
    const v = parseInt(e.target.dataset.v || 0, 10);
    if (v) set(v);
  });
});

// Toggle reseña
document.addEventListener('click', (e) => {
  const btn = e.target.closest('.review-toggle');
  if (!btn) return;
  const sel = btn.getAttribute('data-target');
  const panel = document.querySelector(sel);
  if (!panel) return;
  const hidden = panel.hasAttribute('hidden');
  if (hidden) { panel.removeAttribute('hidden'); btn.setAttribute('aria-expanded','true'); }
  else { panel.setAttribute('hidden',''); btn.setAttribute('aria-expanded','false'); }
});
</script>
@endsection
