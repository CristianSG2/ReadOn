# ReadOn

📚 Aplicación web para registrar y reseñar libros, inspirada en Letterboxd pero orientada a lecturas.  
Proyecto personal de portfolio, construido con un stack sencillo y mantenible.

---

## 🚀 Stack

- **Backend**: Laravel 11 + Blade  
- **Base de datos**: PostgreSQL (vía DDEV)  
- **Frontend**: SCSS compilado con Vite (sin Tailwind ni Bootstrap)  
- **Tooling**: Node.js 20 (Vite para assets)  
- **Entorno**: DDEV (Docker), PHP 8.2, nginx-fpm

---

## ⚙️ Puesta en marcha (local)

Requisitos: DDEV → https://ddev.readthedocs.io/en/stable/

```bash
# Clonar repositorio
git clone git@github.com:CristianSG2/ReadOn.git
cd ReadOn

# Iniciar entorno
ddev start

# Dependencias PHP
ddev composer install

# Dependencias JS
ddev npm install

# Clave de aplicación
ddev artisan key:generate

# Migraciones (users, sessions, etc.)
ddev artisan migrate

# Compilar assets (producción) — o usa `ddev npm run dev` para desarrollo
ddev npm run build

# Abrir
ddev launch   # https://readon.ddev.site
```

**Notas**  
- Ajusta `APP_URL` a `https://readon.ddev.site` (o tu host DDEV).  
- `public/build/*` está ignorado en `.gitignore` (assets generados por Vite).

---

## 🔐 Autenticación (manual, sin Breeze)

- **Rutas**:  
  `GET /login`, `POST /login`, `GET /register`, `POST /register`,  
  `POST /logout`, `GET /me` (protegida)
- **Seguridad**:
  - CSRF en formularios.
  - `session()->regenerate()` tras login.
  - `logout`: `invalidate()` + `regenerateToken()`.
  - **Throttle** de login: 5 intentos/min por email+IP.
- **Redirecciones**:
  - Con sesión, `/login` y `/register` → **/me**.
  - Sin sesión, `/me` → **/login**.

---

## 🧭 Rutas clave

```text
GET  /login      → Auth\LoginController@showLoginForm
POST /login      → Auth\LoginController@login      (throttle:login)
GET  /register   → Auth\RegisterController@showRegisterForm
POST /register   → Auth\RegisterController@register
POST /logout     → Auth\LoginController@logout
GET  /me         → (auth) vista de perfil
```

---

## 📂 Estructura relevante

- `resources/views/layouts/app.blade.php` — layout principal (cabecera/nav)  
- `resources/views/welcome.blade.php` — landing mínima (tema oscuro)  
- `resources/views/auth/login.blade.php` — formulario de login  
- `resources/views/auth/register.blade.php` — formulario de registro  
- `resources/views/me.blade.php` — perfil protegido  
- `resources/scss/app.scss` — estilos (paleta en variables CSS)  
- `resources/js/app.js` — entrada JS para Vite

---

## ✅ Estado actual

- ✔️ Laravel 11 + PostgreSQL (DDEV) funcionando  
- ✔️ Vite para SCSS/JS configurado  
- ✔️ **Auth manual**: login/registro/logout + `/me` protegida  
- ✔️ Tema oscuro base (sin frameworks CSS)

---

## 📑 Próximos pasos

- **Google Books API** → servicio PHP, búsqueda de libros, ficha detalle.  
- **Logs de lectura** → modelo `logs`, migración y CRUD básico (estado/rating/reseña por usuario).  
- **Perfil de usuario** → página “Mi perfil” con lecturas guardadas y estadísticas simples.  
- **Estilos con Sass** → organización en _partials_ (variables, layout, componentes: cards, botones, inputs).  
- **Mejoras de UX** → paginación en búsqueda, mensajes *flash*, validaciones en frontend.  
- **Deploy** → Koyeb / Render, `.env` de producción, `artisan config:cache`, `route:cache`, etc.  
- **Documentación** → README final, capturas de pantalla e instrucciones para portfolio.

---

## 🧰 Scripts útiles

```bash
# Desarrollo (watch)
ddev npm run dev

# Build de producción
ddev npm run build

# Limpiar cachés de Laravel (rutas/config/views)
ddev artisan optimize:clear
```

---

## 🛠️ Troubleshooting

1) **Vite**: “Unable to locate file in Vite manifest: resources/css/app.css”  
   → Usa `@vite(['resources/scss/app.scss','resources/js/app.js'])` y ejecuta `ddev npm run build`.

2) **/login y /register no redirigen a /me con sesión**  
   → En `bootstrap/app.php`, alias:  
   `guest => App\Http\Middleware\RedirectIfAuthenticated::class`.  
   Limpia cachés: `ddev artisan optimize:clear`.

3) **/me no protege sin sesión**  
   → En `bootstrap/app.php`, alias:  
   `auth => App\Http\Middleware\Authenticate::class`.  
   En `Authenticate::redirectTo()`, devuelve `route('login')`.

4) **Throttle no aplica**  
   → Define `RateLimiter::for('login', ...)` en `AppServiceProvider::boot()`  
   y aplica `->middleware('throttle:login')` a `POST /login`.

---

## Licencia

<<<<<<< HEAD
Este proyecto se distribuye bajo la **MIT License**.  
Consulta el archivo [LICENSE](./LICENSE) para más detalles.
=======
Proyecto personal. Sin licencia pública definida por ahora.
>>>>>>> origin/main
