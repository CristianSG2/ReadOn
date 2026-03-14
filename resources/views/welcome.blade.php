<!doctype html>
<html lang="es" data-theme="dark">
<head>
  <meta charset="utf-8">
  <title>{{ config('app.name','ReadOn') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script>(function(){var t=localStorage.getItem('theme')||'dark';var el=document.documentElement;el.setAttribute('data-theme',t);el.classList.add('no-transitions');setTimeout(function(){el.classList.remove('no-transitions');},100);})();</script>
  @vite(['resources/scss/app.scss','resources/js/app.js'])
</head>
<body class="landing">

<main class="container">
  <section class="landing-frame landing-frame--full">

    {{-- Topbar --}}
    <div class="landing-topbar">
      <a href="{{ url('/') }}"><x-logo size="sm" /></a>
      <div class="landing-topbar__actions">
        <button id="theme-toggle" class="theme-toggle" aria-label="Activar tema claro" title="Cambiar tema">☀</button>
        @guest
          <a class="btn btn-outline landing-login-btn" href="{{ url('/login') }}">Entrar</a>
        @endguest
        @auth
          <a class="landing-login" href="{{ route('me') }}">Mi perfil</a>
        @endauth
      </div>
    </div>

    {{-- Hero --}}
    <div class="landing-hero">
      <div class="landing-hero__content">
        <h1>Tu biblioteca.<br><span>Tu historia.</span></h1>
        <p>Guarda, organiza y valora cada libro que lees, en un solo lugar.</p>

        @guest
          <div class="actions">
            <a class="btn btn-primary" href="{{ url('/register') }}">Crear cuenta</a>
            <a class="btn btn-outline" href="{{ url('/login') }}">Entrar</a>
          </div>
        @endguest

        @auth
          <p class="logged-info">Bienvenido de nuevo, <strong>{{ auth()->user()->name }}</strong>.</p>
          <div class="actions">
            <a class="btn btn-primary" href="{{ route('books.index') }}">Ir a mis lecturas</a>
          </div>
        @endauth
      </div>

      {{-- Estantería decorativa --}}
      <div class="landing-hero__visual" aria-hidden="true">
        <svg class="landing-bookshelf" viewBox="0 0 480 320" xmlns="http://www.w3.org/2000/svg">
          <!-- sombra suelo -->
          <ellipse cx="240" cy="306" rx="218" ry="11" fill="var(--border)" fill-opacity=".35"/>
          <!-- tablero estantería -->
          <rect x="0" y="282" width="480" height="9" rx="2" fill="var(--surface-2)"/>
          <!-- grupo 1 -->
          <rect x="16"  y="110" width="26" height="172" rx="2" fill="var(--accent)"   fill-opacity=".82"/>
          <rect x="46"  y="86"  width="18" height="196" rx="2" fill="var(--primary)"  fill-opacity=".78"/>
          <rect x="68"  y="126" width="30" height="156" rx="2" fill="var(--success)"  fill-opacity=".70"/>
          <!-- grupo 2 -->
          <rect x="112" y="94"  width="22" height="188" rx="2" fill="var(--warning)"  fill-opacity=".76"/>
          <rect x="138" y="106" width="28" height="176" rx="2" fill="var(--accent-2)" fill-opacity=".82"/>
          <rect x="170" y="130" width="18" height="152" rx="2" fill="var(--error)"    fill-opacity=".65"/>
          <rect x="192" y="100" width="24" height="182" rx="2" fill="var(--muted)"    fill-opacity=".42"/>
          <!-- grupo 3 -->
          <rect x="232" y="88"  width="30" height="194" rx="2" fill="var(--accent)"   fill-opacity=".68"/>
          <rect x="266" y="114" width="22" height="168" rx="2" fill="var(--primary)"  fill-opacity=".74"/>
          <rect x="292" y="86"  width="26" height="196" rx="2" fill="var(--success)"  fill-opacity=".78"/>
          <!-- grupo 4 -->
          <rect x="334" y="108" width="20" height="174" rx="2" fill="var(--warning)"  fill-opacity=".72"/>
          <rect x="358" y="92"  width="28" height="190" rx="2" fill="var(--accent-2)" fill-opacity=".85"/>
          <rect x="390" y="122" width="24" height="160" rx="2" fill="var(--accent)"   fill-opacity=".60"/>
          <rect x="418" y="96"  width="20" height="186" rx="2" fill="var(--error)"    fill-opacity=".70"/>
          <rect x="442" y="110" width="26" height="172" rx="2" fill="var(--primary)"  fill-opacity=".65"/>
          <!-- reflejos de lomo (decorativo) -->
          <line x1="39"  y1="118" x2="39"  y2="272" stroke="white" stroke-width=".6" stroke-opacity=".16"/>
          <line x1="163" y1="114" x2="163" y2="272" stroke="white" stroke-width=".6" stroke-opacity=".16"/>
          <line x1="317" y1="94"  x2="317" y2="272" stroke="white" stroke-width=".6" stroke-opacity=".16"/>
          <line x1="384" y1="100" x2="384" y2="272" stroke="white" stroke-width=".6" stroke-opacity=".16"/>
        </svg>
      </div>
    </div>

  </section>
</main>

<footer class="site-footer">
  <div class="container">© {{ date('Y') }} ReadOn</div>
</footer>

<script>
(function () {
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
</script>

</body>
</html>
