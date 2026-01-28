@extends('layouts.app')

@section('title', 'Mi perfil')

@section('content')
<div class="container">
    <h1 class="page-title">Mi perfil</h1>

    {{-- Grid de estadísticas rápidas --}}
    <section class="stats-grid" aria-label="Estadísticas de lectura">
        <div class="stat-card">
            <div class="stat-label">Total</div>
            <div class="stat-value">{{ $total }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Lista de deseos</div>
            <div class="stat-value">{{ $wishlist }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Leyendo</div>
            <div class="stat-value">{{ $reading }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Leído</div>
            <div class="stat-value">{{ $read }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Abandonado</div>
            <div class="stat-value">{{ $dropped }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Media de rating</div>
            <div class="stat-value">{{ $avgRating !== null ? $avgRating : '—' }}</div>
        </div>
    </section>

    {{-- Últimos registros (muestro 5 o los que mande el controlador) --}}
    <section class="mt-4">
        <div class="section-header">
            <h2>Últimos registros</h2>
            <a href="{{ route('reading-logs.index') }}" class="btn-outline">Ver todos</a>
        </div>

        {{-- Empty state si no hay recientes --}}
        @if ($recent->isEmpty())
            <div class="empty-state">
                <div class="empty-state__icon" aria-hidden="true">🗂️</div>
                <h3 class="empty-state__title">Sin registros recientes</h3>
                <p class="empty-state__text">Cuando añada lecturas, veré aquí mis últimos movimientos.</p>
                <a class="btn empty-state__cta" href="{{ route('reading-logs.index') }}">Ver mis lecturas</a>
            </div>
        @else
            <ul class="recent-list">
                @foreach ($recent as $log)
                    <li class="recent-item">
                        <span class="recent-title">{{ $log->title }}</span>
                        {{-- Muestro el estado con el label en español si está disponible --}}
                        @php
                            $badgeClass = match($log->status) {
                                'wishlist' => 'badge badge--light badge--wishlist',
                                'reading'  => 'badge badge--light badge--reading',
                                'read'     => 'badge badge--light badge--read',
                                'dropped'  => 'badge badge--light badge--dropped',
                                default    => 'badge badge--light badge--wishlist',
                            };

                            $label = method_exists($log,'getStatusLabelAttribute')
                                ? $log->status_label
                                : ucfirst($log->status);
                        @endphp

                        <span class="recent-meta">| <span class="{{ $badgeClass }}">{{ $label }}</span></span>

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
