@extends('layouts.app')

@section('title', 'Mi perfil')

@section('content')
<div class="container">
    <h1 class="page-title">Mi perfil</h1>

    {{-- Grid de estadísticas --}}
    <section class="stats-grid" aria-label="Estadísticas de lectura">
        <div class="stat-card">
            <div class="stat-label">Total de logs</div>
            <div class="stat-value">{{ $total }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Wishlist</div>
            <div class="stat-value">{{ $wishlist }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Reading</div>
            <div class="stat-value">{{ $reading }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Read</div>
            <div class="stat-value">{{ $read }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Dropped</div>
            <div class="stat-value">{{ $dropped }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Media de rating</div>
            <div class="stat-value">
                {{ $avgRating !== null ? $avgRating : '—' }}
            </div>
        </div>
    </section>

    {{-- Últimos 5 registros --}}
    <section class="mt-4">
        <h2 class="section-title">Últimos 5 registros</h2>
        @if ($recent->isEmpty())
            <p class="muted">Todavía no has añadido ningún registro.</p>
        @else
            <ul class="recent-list">
                @foreach ($recent as $log)
                    <li class="recent-item">
                        <span class="recent-title">{{ $log->title }}</span>
                        <span class="recent-meta">| {{ ucfirst($log->status) }}</span>
                        @if (!is_null($log->rating))
                            <span class="recent-meta">| ⭐ {{ $log->rating }}</span>
                        @endif
                        <span class="recent-meta">| {{ $log->created_at->format('d/m/Y') }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</div>
@endsection
