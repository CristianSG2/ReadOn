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
- Ajusta `APP_URL` a `https://readon.ddev.site`.  
- `public/build/*` está ignorado en `.gitignore` (assets generados por Vite).  
- Recomendado: restringe la API key **solo** a *Books API* en Google Cloud Console.

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
- **Calidad de portadas**: se selecciona la mejor (`extraLarge → large → medium → ...`) y se **mejora** la miniatura con `zoom`.

---

## 📘 Logs de lectura

- **Modelo & migración**: `reading_logs`
  - `user_id` (FK), `volume_id`, `title`, `authors`, `thumbnail_url`
  - `status` (`wishlist | reading | read | dropped`)
  - `rating` (`TINYINT 1–10`)
  - `review` (nullable)
  - `unique (user_id, volume_id)`
- **Controlador**:
  - `POST /reading-logs` → crear/actualizar log.  
  - `GET /reading-logs` → listado “Mis lecturas”.  
  - `PATCH /reading-logs/{log}` → actualizar **status**.  
  - `PATCH /reading-logs/{log}/rating` → actualizar **rating**.  
  - `PATCH /reading-logs/{log}/review` → añadir/editar/eliminar **reseña**.  
  - `DELETE /reading-logs/{log}` → eliminar registro (solo dueño).
- **UI**:
  - Listado con **cards** y portadas nítidas (mejora `zoom`).  
  - **Rating estilo Letterboxd** (5★ con medias → 1..10).  
  - **Estado** editable inline con select.  
  - **Reseñas**: textarea plegable, snippet (140 chars) y flashes de éxito.  
  - **Eliminación**: botón flotante (overlay) sobre la portada con confirmación.  
  - **Alerts** con contraste (`success` y `warning`).

---

## 🧭 Rutas clave (resumen)

```text
GET    /books                      → BookController@index       (throttle:30,1)
GET    /books/{id}                 → BookController@show        (throttle:30,1)

POST   /reading-logs               → ReadingLogController@store    (auth)
GET    /reading-logs               → ReadingLogController@index    (auth)
PATCH  /reading-logs/{log}         → ReadingLogController@update   (auth)
PATCH  /reading-logs/{log}/rating  → ReadingLogController@updateRating (auth)
PATCH  /reading-logs/{log}/review  → ReadingLogController@updateReview (auth)
DELETE /reading-logs/{log}         → ReadingLogController@destroy  (auth)
```

---

## 📂 Estructura relevante

- `resources/views/layouts/app.blade.php` — layout principal  
- `resources/views/books/index.blade.php` — grid de resultados  
- `resources/views/books/show.blade.php` — ficha detalle con botón “Guardar en mis lecturas”  
- `resources/views/reading-logs/index.blade.php` — “Mis lecturas” (estado + rating + reseña + eliminar overlay)  
- `resources/scss/app.scss` — tema oscuro, botones, estrellas, alerts, review-form  
- `app/Http/Controllers/ReadingLogController.php` — lógica de estado, rating, review y eliminación  

---

## ✅ Estado actual

- ✔️ Laravel 11 + PostgreSQL (DDEV)  
- ✔️ Auth manual funcional (`login`, `register`, `logout`, `/me`)  
- ✔️ Integración con Google Books + throttle  
- ✔️ Logs de lectura: creación, edición, estado, rating  
- ✔️ **NUEVO**: reseñas (add/edit/delete)  
- ✔️ **NUEVO**: eliminación con botón flotante (overlay top-right)  
- ✔️ SCSS y layout base en tema oscuro  

---

## 📑 Roadmap

- **Perfil** (`/me`) con estadísticas de lectura (libros, media, top géneros).  
- **Validaciones front** y mejoras UX (mensajes, loading, accesibilidad móvil).  
- **Deploy** → Koyeb/Render, `.env` de producción.  
- **Documentación** → capturas y guía final para portfolio.  
- **Tests** → funcionales (feature tests) para logs y auth.

---

## 🧰 Scripts útiles

```bash
# Desarrollo
ddev npm run dev

# Build de producción
ddev npm run build

# Limpiar cachés de Laravel
ddev artisan optimize:clear
```

---

## 🔗 Pull Requests relevantes

- Books API + vistas + throttle → https://github.com/CristianSG2/ReadOn/pull/8  
- Reading logs + rating + UI/SCSS → https://github.com/CristianSG2/ReadOn/pull/10  
- Reviews + eliminación de logs (overlay UI) → https://github.com/CristianSG2/ReadOn/pull/12

---

## 🛠️ Troubleshooting

1) **Vite**: “Unable to locate file in Vite manifest”  
   → Usa `@vite(['resources/scss/app.scss','resources/js/app.js'])` y ejecuta `ddev npm run build`.

2) **Redirecciones /me**  
   → En `bootstrap/app.php`, alias:  
   `guest => App\Http\Middleware\RedirectIfAuthenticated::class`  
   `auth  => App\Http\Middleware\Authenticate::class`  
   Limpia cachés: `ddev artisan optimize:clear`.

3) **Throttle de /books**  
   → Verifica con `php artisan route:list` que estén dentro de `throttle:30,1`.

---

## Licencia

Este proyecto se distribuye bajo la **MIT License**.  
Consulta el archivo [LICENSE](./LICENSE) para más detalles.
