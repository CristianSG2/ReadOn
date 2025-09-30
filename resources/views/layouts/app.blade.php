<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title','ReadOn')</title>
  @vite(['resources/scss/app.scss','resources/js/app.js'])
</head>
<body>
  <header>
    <h1 style="margin:1rem 0;">ReadOn</h1>
  </header>

  <main>
    @yield('content')
  </main>
</body>
</html>
