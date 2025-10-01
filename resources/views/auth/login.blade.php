@extends('layouts.app')

@section('content')
  <section class="card" style="max-width:560px;margin:2rem auto;">
    <h1 style="margin-top:0;">Iniciar sesión</h1>

    {{-- Mensajes de error global (por ejemplo, credenciales inválidas) --}}
    @if ($errors->any())
      <div style="border:1px solid #532; background:#2a1919; color:#f1d5d5; padding:.75rem; border-radius:10px; margin-bottom:1rem;">
        @foreach ($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" novalidate>
      @csrf

      <div style="margin-bottom:1rem;">
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

      <div style="margin-bottom:1rem;">
        <label for="password">Contraseña</label>
        <input
          id="password"
          name="password"
          type="password"
          required
          autocomplete="current-password"
        >
      </div>

      <div style="display:flex; gap:.75rem; align-items:center;">
        <button class="btn" type="submit">Entrar</button>
        <a class="btn btn-outline" href="{{ route('register') }}">Crear cuenta</a>
      </div>
    </form>
  </section>
@endsection
