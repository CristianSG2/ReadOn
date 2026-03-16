<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}" data-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title','ReadOn')</title>
  <script>(function(){var t=localStorage.getItem('theme')||'dark';var el=document.documentElement;el.setAttribute('data-theme',t);el.classList.add('no-transitions');setTimeout(function(){el.classList.remove('no-transitions');},100);})();</script>
  @vite(['resources/scss/app.scss','resources/js/app.js'])
</head>
<body>
  <header class="site-nav" role="banner">
    <div class="nav">
        <a class="nav__brand" href="{{ url('/') }}"><x-logo size="sm" /></a>

        {{-- Toggle simple para móvil (JS mínimo más abajo en el layout) --}}
        <button class="nav__toggle" id="navToggle" aria-controls="navMenu" aria-expanded="false">
            Menu
        </button>

        <nav id="navMenu" class="nav__links" role="navigation">
            @auth
                <a href="{{ route('books.index') }}" class="nav__link {{ request()->routeIs('books.*') ? 'is-active' : '' }}"
                   @if(request()->routeIs('books.*')) aria-current="page" @endif>Libros</a>

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
            <button id="theme-toggle" class="theme-toggle" aria-label="Activar tema claro" title="Cambiar tema">☀</button>
</header>
  <div id="toast-container" aria-live="polite"></div>
  <script>
  function showToast(message, type) {
    type = type || 'success';
    var container = document.getElementById('toast-container');
    if (!container) return;
    var el = document.createElement('div');
    el.className = 'toast' + (type === 'error' ? ' toast--error' : '');
    el.textContent = message;
    el.style.opacity = '0';
    el.style.transform = 'translateY(8px)';
    container.appendChild(el);
    setTimeout(function () {
      el.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
      el.style.opacity = '1';
      el.style.transform = 'translateY(0)';
    }, 20);
    setTimeout(function () {
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 300);
    }, 3000);
  }
  </script>
  <main>
    @yield('content')
  </main>
  <script>
  (function () {
    // ── Menú móvil ──
    var navBtn = document.getElementById('navToggle');
    var menu   = document.getElementById('navMenu');
    if (navBtn && menu) {
      navBtn.addEventListener('click', function () {
        var isOpen = menu.getAttribute('data-open') === '1';
        menu.setAttribute('data-open', isOpen ? '0' : '1');
        navBtn.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
      });
    }

    // ── Theme toggle ──
    var themeBtn = document.getElementById('theme-toggle');
    if (themeBtn) {
      function updateIcon(theme) {
        themeBtn.textContent = theme === 'dark' ? '☀' : '☽';
        themeBtn.setAttribute('aria-label', theme === 'dark' ? 'Activar tema claro' : 'Activar tema oscuro');
      }
      updateIcon(document.documentElement.getAttribute('data-theme') || 'dark');
      themeBtn.addEventListener('click', function () {
        document.documentElement.classList.add('no-transitions');
        var cur  = document.documentElement.getAttribute('data-theme') || 'dark';
        var next = cur === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        localStorage.setItem('theme', next);
        updateIcon(next);
        setTimeout(function () { document.documentElement.classList.remove('no-transitions'); }, 100);
      });
    }
  })();

  // ── Page transition ──
  (function () {
    var mainEl = document.querySelector('main');
    if (!mainEl) return;

    // Fade-in al cargar (espera a que no-transitions haya sido eliminado)
    setTimeout(function () { mainEl.style.opacity = '1'; }, 120);

    // Fade-out al navegar
    document.addEventListener('click', function (e) {
      var a = e.target.closest('a[href]');
      if (!a || a.target === '_blank') return;
      var href = a.getAttribute('href');
      if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
      try {
        var url = new URL(href, location.href);
        if (url.hostname !== location.hostname) return;
      } catch (_) { return; }
      e.preventDefault();
      mainEl.style.opacity = '0';
      setTimeout(function () { location.href = href; }, 200);
    });
  })();

  // ── Auto-dismiss alerts ──
  (function () {
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (el) {
      // Entrada: estado inicial invisible
      el.style.opacity = '0';
      el.style.transform = 'translateY(-12px)';
      setTimeout(function () {
        el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
        el.style.opacity = '1';
        el.style.transform = 'translateY(0)';
      }, 50);
      // Salida automática tras 3.5s
      setTimeout(function () {
        el.style.opacity = '0';
        setTimeout(function () { el.remove(); }, 400);
      }, 3500);
    });
  })();
  </script>

</body>
</html>
