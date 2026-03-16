@extends('layouts.app')

@section('title', 'Mis lecturas')

@section('content')
<div class="container">
    <h1 class="mb-4">Mis lecturas</h1>

    @if(session('success'))
        <script>showToast('{{ session('success') }}');</script>
    @endif
    @if(session('error'))
        <script>showToast('{{ session('error') }}', 'error');</script>
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
        <div class="logs-grid">
            @foreach($logs as $log)
                @php
                    // Subo la calidad de la miniatura si viene de Google Books
                    $upgrade = $upgrade ?? function (?string $url, int $zoom = 4) {
                        if (!$url) return $url;
                        if (str_contains($url, 'zoom=')) return preg_replace('/zoom=\d+/', 'zoom='.$zoom, $url);
                        if (str_contains($url, 'books.google')) return $url.(str_contains($url, '?') ? '&' : '?').'zoom='.$zoom;
                        return $url;
                    };
                    $cover = $upgrade($log->getCoverUrl(), 4);

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
                    <div class="card-thumb{{ $cover ? ' is-loading' : '' }}">
                        @if($cover)
                            <img src="{{ $cover }}" alt="{{ $log->title }}"
                                 onload="this.closest('.card-thumb').classList.remove('is-loading')"
                                 onerror="this.onerror=null;this.src='{{ asset('images/no-cover.svg') }}';this.closest('.card-thumb').classList.remove('is-loading')">
                        @else
                            <div class="thumb-placeholder">Sin portada</div>
                        @endif

                        {{-- Botón de borrar --}}
                        <form
                            action="{{ route('reading-logs.destroy', $log) }}"
                            method="POST"
                            class="thumb-actions"
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
                            <span class="{{ $badgeClass }}">{{ $log->status_label }}</span>
                            @if(!is_null($log->rating)) · {{ $log->rating }}★ @endif
                        </p>

                        {{-- Selector de estado — autosave vía fetch --}}
                        <div class="mt-2">
                            <label class="label">Estado</label>
                            <select class="input status-select"
                                    data-url="{{ route('reading-logs.update', $log) }}"
                                    data-csrf="{{ csrf_token() }}">
                                <option value="wishlist" @selected($log->status === 'wishlist')>Lista de deseos</option>
                                <option value="reading"  @selected($log->status === 'reading')>Leyendo</option>
                                <option value="read"     @selected($log->status === 'read')>Leído</option>
                                <option value="dropped"  @selected($log->status === 'dropped')>Abandonado</option>
                            </select>
                        </div>

                        {{-- Bloque de rating (escala 0.5–5.0) — autosave vía fetch --}}
                        <div class="mt-3">
                            <label class="label">Rating</label>
                            <div class="stars"
                                 data-initial="{{ $log->rating ?? 0 }}"
                                 data-url="{{ route('reading-logs.rating', $log) }}"
                                 data-csrf="{{ csrf_token() }}">
                                <div class="stars__display" aria-hidden="true">★★★★★</div>
                                <div class="stars__fill" style="width: {{ ($log->rating ?? 0) * 20 }}%;" aria-hidden="true">★★★★★</div>
                                <div class="stars__hit">
                                    @for($i = 1; $i <= 10; $i++)
                                        <button type="button" data-v="{{ $i * 0.5 }}" aria-label="{{ $i * 0.5 }} estrellas"></button>
                                    @endfor
                                </div>
                                <span class="stars__feedback" aria-live="polite"></span>
                            </div>
                            <button type="button" class="btn btn-link stars-clear mt-1"
                                    data-url="{{ route('reading-logs.rating', $log) }}"
                                    data-csrf="{{ csrf_token() }}">Quitar rating</button>
                        </div>

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

{{-- JS: rating autosave, estado autosave, toggle reseña --}}
<script>
// ── Utilidad fetch PATCH ──────────────────────────────────────────────────────
function patchJson(url, csrf, body) {
  return fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-HTTP-Method-Override': 'PATCH',
      'X-CSRF-TOKEN': csrf,
    },
    body: JSON.stringify(body),
  }).then(r => { if (!r.ok) throw new Error(r.status); return r.json(); });
}

// ── Rating: autosave al click ─────────────────────────────────────────────────
document.querySelectorAll('.stars').forEach(stars => {
  const fill = stars.querySelector('.stars__fill');
  const hit  = stars.querySelector('.stars__hit');
  const url  = stars.dataset.url;
  const csrf = stars.dataset.csrf;
  let current = parseFloat(stars.dataset.initial) || 0;

  const applyWidth = v => { fill.style.width = (v * 20) + '%'; };

  hit.addEventListener('mousemove', e => {
    const v = parseFloat(e.target.dataset.v || 0);
    if (v) applyWidth(v);
  });
  hit.addEventListener('mouseleave', () => applyWidth(current));
  hit.addEventListener('click', e => {
    const v = parseFloat(e.target.dataset.v || 0);
    if (!v) return;
    const prev = current;
    current = v;
    applyWidth(current);
    patchJson(url, csrf, { rating: current })
      .then(() => { stars.dataset.initial = current; showToast('Rating guardado'); })
      .catch(() => { current = prev; applyWidth(current); showToast('Error al guardar', 'error'); });
  });
});

// ── Quitar rating ─────────────────────────────────────────────────────────────
document.querySelectorAll('.stars-clear').forEach(btn => {
  btn.addEventListener('click', () => {
    const url  = btn.dataset.url;
    const csrf = btn.dataset.csrf;
    const stars = btn.closest('.mt-3').querySelector('.stars');
    const fill  = stars ? stars.querySelector('.stars__fill') : null;
    patchJson(url, csrf, { rating: null })
      .then(() => {
        if (stars) stars.dataset.initial = 0;
        if (fill)  fill.style.width = '0%';
        showToast('Rating eliminado');
      })
      .catch(() => { showToast('Error al guardar', 'error'); });
  });
});

// ── Estado: autosave al cambiar el select ─────────────────────────────────────
document.querySelectorAll('.status-select').forEach(sel => {
  const url  = sel.dataset.url;
  const csrf = sel.dataset.csrf;
  let prev   = sel.value;
  sel.addEventListener('change', () => {
    const next = sel.value;
    patchJson(url, csrf, { status: next })
      .then(() => { prev = next; showToast('Estado actualizado'); })
      .catch(() => { sel.value = prev; showToast('Error al guardar', 'error'); });
  });
});

// ── Eliminar registro via fetch ───────────────────────────────────────────────
document.querySelectorAll('.thumb-actions').forEach(form => {
  form.addEventListener('submit', e => {
    e.preventDefault();
    if (!confirm('¿Seguro que quieres eliminar este registro?')) return;
    const url  = form.action;
    const csrf = form.querySelector('input[name="_token"]').value;
    const card = form.closest('.card');
    fetch(url, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrf,
        'X-HTTP-Method-Override': 'DELETE',
      },
    })
    .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
    .then(res => {
      showToast(res.data.message, res.ok ? 'success' : 'error');
      if (res.ok && card) {
        card.style.transition = 'opacity 0.3s ease';
        card.style.opacity = '0';
        setTimeout(() => card.remove(), 300);
      }
    })
    .catch(() => showToast('Error al eliminar', 'error'));
  });
});

// ── Reseña: guardado via fetch ────────────────────────────────────────────────
document.querySelectorAll('.review-form__inner').forEach(form => {
  form.addEventListener('submit', e => {
    e.preventDefault();
    const url    = form.action;
    const csrf   = form.querySelector('input[name="_token"]').value;
    const review = form.querySelector('textarea[name="review"]').value;
    const card   = form.closest('.card');

    patchJson(url, csrf, { review })
      .then(data => {
        showToast(data.message);

        // Actualizar o crear/eliminar el snippet
        const snippetEl  = card.querySelector('.review-snippet');
        const toggleBtn  = card.querySelector('.review-toggle');
        if (data.review) {
          const text     = data.review;
          const truncated = text.length > 140 ? text.substring(0, 140) : text;
          const ellipsis  = text.length > 140 ? '<span class="muted">…</span>' : '';
          const html      = '<strong>Reseña:</strong> ' + truncated + ellipsis;
          if (snippetEl) {
            snippetEl.innerHTML = html;
          } else {
            const el = document.createElement('div');
            el.className = 'review-snippet mt-2';
            el.innerHTML = html;
            toggleBtn.parentNode.insertBefore(el, toggleBtn);
          }
        } else {
          if (snippetEl) snippetEl.remove();
        }

        // Cerrar panel y actualizar botón toggle
        const panel = form.closest('.review-form');
        panel.setAttribute('hidden', '');
        toggleBtn.setAttribute('aria-expanded', 'false');
        toggleBtn.textContent = data.review ? 'Editar reseña' : 'Añadir reseña';
      })
      .catch(() => { showToast('Error al guardar', 'error'); });
  });
});

// ── Toggle reseña ─────────────────────────────────────────────────────────────
document.addEventListener('click', e => {
  const btn = e.target.closest('.review-toggle');
  if (!btn) return;
  const panel = document.querySelector(btn.getAttribute('data-target'));
  if (!panel) return;
  const hidden = panel.hasAttribute('hidden');
  if (hidden) { panel.removeAttribute('hidden'); btn.setAttribute('aria-expanded', 'true'); }
  else        { panel.setAttribute('hidden', ''); btn.setAttribute('aria-expanded', 'false'); }
});
</script>
@endsection
