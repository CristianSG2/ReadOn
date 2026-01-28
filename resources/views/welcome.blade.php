<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>{{ config('app.name','ReadOn') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @vite(['resources/scss/app.scss','resources/js/app.js'])
</head>
<body class="landing">

<main class="container">
  <section class="landing-frame landing-frame--full">
    {{-- Header interno --}}
    <div class="landing-topbar">
      <div class="landing-logo">ReadOn</div>

      @guest
        <a class="btn btn-outline landing-login-btn" href="{{ url('/login') }}">Entrar</a>
      @endguest

      @auth
        <a class="landing-login" href="{{ url('/me') }}">Mi perfil</a>
      @endauth
    </div>

    {{-- Hero --}}
    <div class="landing-hero">
      <div class="landing-hero__content">
        <h1>Bienvenido a <span>ReadOn</span></h1>
        <p>
          Tu biblioteca personal de lectura.
          Guarda, valora y organiza tus libros en un solo sitio.
        </p>

        @guest
          <div class="actions">
            <a class="btn btn-primary" href="{{ url('/register') }}">
              Crear cuenta
            </a>
          </div>
        @endguest

        @auth
          <p class="logged-info">
            Estás logueado como <strong>{{ auth()->user()->email }}</strong>.
          </p>
          <a class="btn btn-primary" href="{{ url('/me') }}">Ir a mi perfil</a>
        @endauth
      </div>

      {{-- Visual --}}
      <div class="landing-hero__visual" aria-hidden="true"></div>
    </div>
  </section>
</main>


  <footer class="site-footer">
    <div class="container">© {{ date('Y') }} ReadOn</div>
  </footer>

</body>
</html>
