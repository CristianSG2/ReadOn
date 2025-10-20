<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title','ReadOn')</title>
  @vite(['resources/scss/app.scss','resources/js/app.js'])
</head>
<body>
  <header class="site-nav" role="banner">
    <div class="nav">
        <a class="nav__brand" href="{{ url('/') }}">ReadOn</a>

        {{-- Toggle simple para móvil (JS mínimo más abajo en el layout) --}}
        <button class="nav__toggle" id="navToggle" aria-controls="navMenu" aria-expanded="false">
            Menu
        </button>

        <nav id="navMenu" class="nav__links" role="navigation">
            @auth
                <a href="{{ route('books.index') }}" class="nav__link {{ request()->routeIs('books.*') ? 'is-active' : '' }}"
                   @if(request()->routeIs('books.*')) aria-current="page" @endif>Books</a>

                <a href="{{ route('reading-logs.index') }}" class="nav__link {{ request()->routeIs('reading-logs.*') ? 'is-active' : '' }}"
                   @if(request()->routeIs('reading-logs.*')) aria-current="page" @endif>Mis lecturas</a>

                <a href="{{ route('me') }}" class="nav__link {{ request()->routeIs('me') ? 'is-active' : '' }}"
                   @if(request()->routeIs('me')) aria-current="page" @endif>Perfil</a>

                {{-- Logout: form POST con @csrf; el botón se estiliza como enlace --}}
                <form method="POST" action="{{ route('logout') }}" class="nav__logout-form">
                    @csrf
                    <button type="submit" class="nav__link nav__logout">Salir</button>
                </form>
            @endauth

            @guest
                <a href="{{ url('/') }}" class="nav__link {{ request()->is('/') ? 'is-active' : '' }}"
                   @if(request()->is('/')) aria-current="page" @endif>Inicio</a>

                <a href="{{ route('login') }}" class="nav__link {{ request()->routeIs('login') ? 'is-active' : '' }}"
                   @if(request()->routeIs('login')) aria-current="page" @endif>Login</a>

                <a href="{{ route('register') }}" class="nav__link {{ request()->routeIs('register') ? 'is-active' : '' }}"
                   @if(request()->routeIs('register')) aria-current="page" @endif>Registro</a>
            @endguest
        </nav>
    </div>
</header>
  <main>
    @yield('content')
  </main>
  <script>
    // JS mínimo y sin dependencias para el menú móvil
    (function () {
        const btn = document.getElementById('navToggle');
        const menu = document.getElementById('navMenu');
        if (!btn || !menu) return;
        btn.addEventListener('click', function () {
            const isOpen = menu.getAttribute('data-open') === '1';
            menu.setAttribute('data-open', isOpen ? '0' : '1');
            btn.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        });
      })();
  </script>

</body>
</html>
