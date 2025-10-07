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

# Variable de Google Books (añade tu API key en .env)
# GOOGLE_BOOKS_API_KEY=xxxx

# Migraciones (users, sessions, reading_logs, etc.)
ddev artisan migrate

# Compilar assets (producción) — o usa `ddev npm run dev` para desarrollo
ddev npm run build

# Abrir
ddev launch   # https://readon.ddev.site
```

**Notas**  
- Ajusta `APP_URL` a `https://readon.ddev.site` (o tu host DDEV).  
- `public/build/*` está ignorado en `.gitignore` (assets generados por Vite).  
- Recomendado: en Google Cloud Console, restringe la API key **solo** a *Books API* (API restrictions).

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

## 🔎 Integración: Google Books

- **Servicio PHP** (`app/Services/GoogleBooks.php`): búsquedas y detalle de volúmenes usando `Http` de Laravel con:
  - **Timeout** 5s y **manejo de errores** (status ≠ 200).  
  - **Caché** en búsquedas y detalles (15 min).  
- **Variables**:
  ```env
  GOOGLE_BOOKS_API_KEY=tu_clave_real
  ```
- **Rutas públicas (solo lectura)**:
  - `GET /books` → búsqueda (paginada).  
  - `GET /books/{id}` → ficha detalle.  
- **Protección de cuota**: `throttle:30,1` aplicado al grupo de `/books` (30 req/min/IP).  
- **Calidad de portadas**: se selecciona la mejor (`extraLarge → large → medium → ...`) y se **mejora** la miniatura de Google con parámetro `zoom` cuando procede.

---

## 📘 Logs de lectura

- **Modelo & migración**: `reading_logs`
  - `user_id` (FK), `volume_id` (Google Books), `title`, `authors`, `thumbnail_url`
  - `status` (`want | reading | read | dropped`), `rating` (`TINYINT 1–10`), `review` (opcional, pendiente)
  - `unique (user_id, volume_id)`
- **Controlador**:
  - `POST /reading-logs` (auth) → crear/actualizar log (por defecto `want`).  
  - `GET  /reading-logs` (auth) → listado de “Mis lecturas”.  
  - `PATCH /reading-logs/{readingLog}` (auth) → actualizar **status**.  
  - `PATCH /reading-logs/{readingLog}/rating` (auth) → actualizar **rating**.
- **UI**:
  - Listado con **cards** y portadas nítidas (mejora de `zoom`).  
  - **Rating estilo Letterboxd** (5★ con medias) → envía 1..10.  
  - Fila de **estado** con layout consistente (sin desbordes).  
  - **Alerts** con buen contraste para mensajes *flash*.

---

## 🧭 Rutas clave (resumen)

```text
GET  /books               → BookController@index     (throttle:30,1)
GET  /books/{id}          → BookController@show      (throttle:30,1)

POST /reading-logs        → ReadingLogController@store    (auth)
GET  /reading-logs        → ReadingLogController@index    (auth)
PATCH /reading-logs/{log} → ReadingLogController@update   (auth)      # cambia status
PATCH /reading-logs/{log}/rating → ReadingLogController@updateRating  (auth)
```

---

## 📂 Estructura relevante

- `resources/views/layouts/app.blade.php` — layout principal (cabecera/nav)  
- `resources/views/books/index.blade.php` — **grid compacto** de resultados  
- `resources/views/books/show.blade.php` — ficha detalle con botón “Guardar en mis lecturas”  
- `resources/views/reading-logs/index.blade.php` — **Mis lecturas** (estado + rating 0.5★)  
- `resources/scss/app.scss` — estilos base, alerts, estrellas y utilidades  
- `resources/js/app.js` — entrada JS para Vite

---

## ✅ Estado actual

- ✔️ Laravel 11 + PostgreSQL (DDEV) funcionando  
- ✔️ Vite para SCSS/JS configurado  
- ✔️ **Auth manual**: login/registro/logout + `/me` protegida  
- ✔️ **Google Books**: servicio + búsqueda + detalle (+ rate limit)  
- ✔️ **Reading logs**: crear desde ficha, ver listado, cambiar **estado** y **rating** (0.5★)  
- ✔️ Tema oscuro base y componentes mínimos (cards, botones, inputs)

---

## 📑 Roadmap

- **Review** en logs (texto) y edición inline.  
- **Perfil** (`/me`) con estadísticas simples (libros leídos, media de rating, top géneros).  
- **Validaciones front** y UX (mensajes, *loading*, errores de red).  
- **Deploy** → Koyeb/Render, `.env` de producción, `artisan config:cache` / `route:cache`.  
- **Docs** → capturas y guía final para portfolio.

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

## 🔗 Pull Requests relevantes

- Books API + vistas + throttle → https://github.com/CristianSG2/ReadOn/pull/8  
- Reading logs + rating + UI/SCSS → https://github.com/CristianSG2/ReadOn/pull/10

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

4) **Throttle de /books** no parece aplicar  
   → Comprueba con `php artisan route:list` que las rutas `/books` están dentro del grupo `throttle:30,1`.

---

## Licencia

Este proyecto se distribuye bajo la **MIT License**.  
Consulta el archivo [LICENSE](./LICENSE) para más detalles.
