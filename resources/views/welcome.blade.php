<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>{{ config('app.name','ReadOn') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @vite(['resources/scss/app.scss','resources/js/app.js'])
</head>
<body>
  <header class="site-header">
    <div class="container">
      <strong>ReadOn</strong>
      <nav class="nav">
        <a href="{{ url('/') }}">Inicio</a>
        @guest
          <a href="{{ url('/login') }}">Login</a>
          <a href="{{ url('/register') }}">Registro</a>
        @endguest
        @auth
          <a href="{{ url('/me') }}">Mi perfil</a>
          <form action="{{ url('/logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-link">Logout</button>
          </form>
        @endauth
      </nav>
    </div>
  </header>

  <main class="container">
    <section class="card hero">
      <h1>Bienvenido a <span>ReadOn</span></h1>
      <p>Tu biblioteca personal de lectura. Próximamente: login, registro y más.</p>

      @guest
        <div class="actions">
          <a class="btn" href="{{ url('/login') }}">Entrar</a>
          <a class="btn btn-outline" href="{{ url('/register') }}">Crear cuenta</a>
        </div>
      @endguest

      @auth
        <p>Estás logueado como <strong>{{ auth()->user()->email }}</strong>.</p>
        <a class="btn" href="{{ url('/me') }}">Ir a mi perfil</a>
      @endauth
    </section>
  </main>

  <footer class="site-footer">
    <div class="container">© {{ date('Y') }} ReadOn</div>
  </footer>
</body>
</html>
