@extends('layouts.app')

@section('content')
  <div class="auth-page">
    <section class="auth-frame card">
      <div class="auth-grid">
        <div class="auth-content">
          <h1>Iniciar sesión</h1>

          {{-- Mensajes de error global (por ejemplo, credenciales inválidas) --}}
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
                required
                autocomplete="current-password"
              >
            </div>

            <div class="auth-actions">
              <button class="btn" type="submit">Entrar</button>
              <a class="btn btn-outline" href="{{ route('register') }}">Crear cuenta</a>
            </div>
          </form>
        </div>

        <div class="auth-visual" aria-hidden="true"></div>
      </div>
    </section>
  </div>
@endsection
