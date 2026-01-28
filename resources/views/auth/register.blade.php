@extends('layouts.app')

@section('content')
  <div class="auth-page">
    <section class="auth-frame card">
      <div class="auth-grid">
        <div class="auth-content">
          <h1>Crear cuenta</h1>

          {{-- Errores de validación (lista global) --}}
          @if ($errors->any())
            <div class="auth-errors">
              @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
              @endforeach
            </div>
          @endif

          <form method="POST" action="{{ route('register.post') }}" novalidate>
            @csrf

            <div class="auth-field">
              <label for="name">Nombre</label>
              <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name') }}"
                required
                autocomplete="name"
              >
            </div>

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
                autocomplete="new-password"
              >
            </div>

            <div class="auth-field">
              <label for="password_confirmation">Repite la contraseña</label>
              <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                required
                autocomplete="new-password"
              >
            </div>

            <div class="auth-actions">
              <button class="btn" type="submit">Crear cuenta</button>
              <a class="btn btn-outline" href="{{ route('login') }}">Ya tengo cuenta</a>
            </div>
          </form>
        </div>

        <div class="auth-visual" aria-hidden="true"></div>
      </div>
    </section>
  </div>
@endsection
