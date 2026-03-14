@extends('layouts.app')

@section('content')
  <div class="auth-page">
    <section class="auth-frame card">
      <div class="auth-grid">
        <div class="auth-content">
          <h1>Iniciar sesión</h1>

          {{-- Mensajes de error global --}}
          @if ($errors->any())
            <div class="auth-errors">
              @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
              @endforeach
            </div>
          @endif

          <form method="POST" action="{{ route('login.post') }}" novalidate>
            @csrf

            <div class="auth-field">
              <label for="email">Email</label>
              <input
                id="email"
                name="email"
                type="email"
                class="input"
                value="{{ old('email') }}"
                required
                autocomplete="email"
              >
            </div>

            <div class="auth-field">
              <label for="password">Contraseña</label>
              <input
                id="password"
                name="password"
                type="password"
                class="input"
                required
                autocomplete="current-password"
              >
            </div>

            <div class="auth-actions">
              <button class="btn btn-primary" type="submit">Entrar</button>
              <a class="btn btn-outline" href="{{ route('register') }}">Crear cuenta</a>
            </div>
          </form>
        </div>

        <div class="auth-visual" aria-hidden="true">
          <svg class="landing-bookshelf" viewBox="0 0 480 320" xmlns="http://www.w3.org/2000/svg">
            <ellipse cx="240" cy="306" rx="218" ry="11" fill="var(--border)" fill-opacity=".35"/>
            <rect x="0" y="282" width="480" height="9" rx="2" fill="var(--surface-2)"/>
            <rect x="16"  y="110" width="26" height="172" rx="2" fill="var(--accent)"   fill-opacity=".82"/>
            <rect x="46"  y="86"  width="18" height="196" rx="2" fill="var(--primary)"  fill-opacity=".78"/>
            <rect x="68"  y="126" width="30" height="156" rx="2" fill="var(--success)"  fill-opacity=".70"/>
            <rect x="112" y="94"  width="22" height="188" rx="2" fill="var(--warning)"  fill-opacity=".76"/>
            <rect x="138" y="106" width="28" height="176" rx="2" fill="var(--accent-2)" fill-opacity=".82"/>
            <rect x="170" y="130" width="18" height="152" rx="2" fill="var(--error)"    fill-opacity=".65"/>
            <rect x="192" y="100" width="24" height="182" rx="2" fill="var(--muted)"    fill-opacity=".42"/>
            <rect x="232" y="88"  width="30" height="194" rx="2" fill="var(--accent)"   fill-opacity=".68"/>
            <rect x="266" y="114" width="22" height="168" rx="2" fill="var(--primary)"  fill-opacity=".74"/>
            <rect x="292" y="86"  width="26" height="196" rx="2" fill="var(--success)"  fill-opacity=".78"/>
            <rect x="334" y="108" width="20" height="174" rx="2" fill="var(--warning)"  fill-opacity=".72"/>
            <rect x="358" y="92"  width="28" height="190" rx="2" fill="var(--accent-2)" fill-opacity=".85"/>
            <rect x="390" y="122" width="24" height="160" rx="2" fill="var(--accent)"   fill-opacity=".60"/>
            <rect x="418" y="96"  width="20" height="186" rx="2" fill="var(--error)"    fill-opacity=".70"/>
            <rect x="442" y="110" width="26" height="172" rx="2" fill="var(--primary)"  fill-opacity=".65"/>
            <line x1="39"  y1="118" x2="39"  y2="272" stroke="white" stroke-width=".6" stroke-opacity=".16"/>
            <line x1="163" y1="114" x2="163" y2="272" stroke="white" stroke-width=".6" stroke-opacity=".16"/>
            <line x1="317" y1="94"  x2="317" y2="272" stroke="white" stroke-width=".6" stroke-opacity=".16"/>
            <line x1="384" y1="100" x2="384" y2="272" stroke="white" stroke-width=".6" stroke-opacity=".16"/>
          </svg>
        </div>
      </div>
    </section>
  </div>
@endsection
