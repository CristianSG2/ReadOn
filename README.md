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

Notas  
- Ajusta APP_URL a https://readon.ddev.site.  
- public/build/* está ignorado en .gitignore (assets generados por Vite).  
- Recomendado: restringe la API key solo a Books API en Google Cloud Console.

---

## 🔐 Autenticación (manual, sin Breeze)

- Rutas:  
  GET /login, POST /login, GET /register, POST /register,  
  POST /logout, GET /me (protegida)
- Seguridad:
  - CSRF en formularios.
  - session()->regenerate() tras login.
  - logout: invalidate() + regenerateToken().
  - Throttle de login: 5 intentos/min por email+IP.
- Redirecciones:
  - Con sesión, /login y /register → /me.
  - Sin sesión, /me → /login.

---

## 🧭 Navbar global

- Implementado en `resources/views/layouts/app.blade.php`.  
- Header fijo superior con enlaces dinámicos según autenticación:

  **Usuarios autenticados** → Books (/books), Mis lecturas (/reading-logs), Perfil (/me), Salir (POST /logout)  
  **Visitantes** → Inicio (/), Login (/login), Registro (/register)

- Incluye:
  - Formulario POST de logout con @csrf integrado.  
  - Estado activo mediante `request()->routeIs(...)` y `.is-active`.  
  - Responsive básico con toggle “Menu” (JS inline, sin dependencias).  
  - Accesibilidad: `aria-controls`, `aria-expanded`, `aria-current`.  
  - Estilo coherente con el tema oscuro (fondo, bordes, hover, foco).  
  - Offset global en `body` para evitar corte del contenido bajo el header.

📸 Capturas
- Navbar fijo en escritorio  
- Navbar colapsado en móvil

---

## 🔎 Integración: Google Books

- Servicio PHP (app/Services/GoogleBooks.php): búsquedas y detalle de volúmenes usando Http de Laravel con:
  - Timeout 5s y manejo de errores (status ≠ 200).  
  - Caché en búsquedas y detalles (15 min).  
- Variables:
  GOOGLE_BOOKS_API_KEY=tu_clave_real
- Rutas públicas (solo lectura):
  GET /books → búsqueda (paginada).  
  GET /books/{id} → ficha detalle.  
- Protección de cuota: throttle:30,1 aplicado al grupo de /books (30 req/min/IP).  
- Calidad de portadas: se selecciona la mejor (extraLarge → large → medium → ...) y se mejora la miniatura con zoom.

---

## 📘 Logs de lectura

- Modelo & migración: reading_logs
  - user_id (FK), volume_id, title, authors, thumbnail_url
  - status (wishlist | reading | read | dropped)
  - rating (TINYINT 1–10)
  - review (nullable)
  - unique (user_id, volume_id)
- Controlador:
  - POST /reading-logs → crear/actualizar log.  
  - GET /reading-logs → listado “Mis lecturas”.  
  - PATCH /reading-logs/{log} → actualizar status.  
  - PATCH /reading-logs/{log}/rating → actualizar rating.  
  - PATCH /reading-logs/{log}/review → añadir/editar/eliminar reseña.  
  - DELETE /reading-logs/{log} → eliminar registro (solo dueño).
- UI:
  - Listado con cards y portadas nítidas (mejora zoom).  
  - Rating estilo Letterboxd (5★ con medias → 1..10).  
  - Estado editable inline con select.  
  - Reseñas: textarea plegable, snippet (140 chars) y flashes de éxito.  
  - Eliminación: botón flotante (overlay) sobre la portada con confirmación.  
  - Alerts con contraste (success y warning).

---

## 👤 Perfil del usuario (/me)

- Controlador: ProfileController@index  
  - Calcula totales por estado (wishlist, reading, read, dropped).  
  - Calcula media de rating (1 decimal, “—” si no hay datos).  
  - Muestra los 5 registros más recientes del usuario con título, estado, rating y fecha.
- Vista: resources/views/profile/index.blade.php  
  - Grid de estadísticas (total, wishlist, reading, read, dropped, rating medio).  
  - Lista de últimos registros con enlace a “Ver todos mis logs”.  
  - Diseño minimalista en tema oscuro.  
- SCSS (app.scss):
  - Bloque .stats-grid, .stat-card, .recent-list.  
  - Micro-UX: animaciones sutiles en hover (transform, box-shadow).

---

## 📑 Estado actual

- ✔️ Laravel 11 + PostgreSQL (DDEV)  
- ✔️ Auth manual funcional (login, register, logout, /me)  
- ✔️ Integración con Google Books + throttle  
- ✔️ Logs de lectura: creación, edición, estado, rating  
- ✔️ Reseñas (add/edit/delete) + eliminación con overlay  
- ✔️ SCSS y layout base en tema oscuro  
- ✔️ Perfil /me con estadísticas de lectura  
- ✔️ **Navbar global fijo con estado activo, responsive y micro-UX**

---

## 🧭 Roadmap

- **UX y visuales**
  - Badges por estado de lectura.  
  - Mensajes de vacíos más claros.  
  - Transiciones suaves y feedback visual.  
  - Botón “Ver todos” en /me hacia /reading-logs.  
  - Ajustes de color y tipografía.

- **Exportación**
  - Endpoint protegido para exportar los reading logs (JSON/CSV).  
  - Botón en /me para descarga directa (stream).

- **Limpieza SCSS**
  - Migrar a partials: `_buttons.scss`, `_cards.scss`, `_alerts.scss`, `_nav.scss`, `_profile.scss`.  

- **Documentación**
  - README final con capturas y sección “Funcionalidades”.  
  - Guía de despliegue y variables de entorno.

- **Deploy**
  - Render o Koyeb, pipeline simple, APP_URL correcto, build Vite.  
  - Cache de config y rutas.

- **(Opcional) Tests**
  - Feature tests para auth, reading logs y /me.

---

## 🔗 Pull Requests relevantes

- Books API + vistas + throttle → https://github.com/CristianSG2/ReadOn/pull/8  
- Reading logs + rating + UI/SCSS → https://github.com/CristianSG2/ReadOn/pull/10  
- Reviews + eliminación de logs (overlay UI) → https://github.com/CristianSG2/ReadOn/pull/12  
- Perfil /me con estadísticas de lectura → https://github.com/CristianSG2/ReadOn/pull/13  
- **Navbar global con enlaces dinámicos y diseño responsive** → https://github.com/CristianSG2/ReadOn/pull/14

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

## 🛠️ Troubleshooting

1) Vite: “Unable to locate file in Vite manifest”  
   → Usa @vite(['resources/scss/app.scss','resources/js/app.js']) y ejecuta ddev npm run build.

2) Redirecciones /me  
   → En bootstrap/app.php, alias:  
   guest => App\Http\Middleware\RedirectIfAuthenticated::class  
   auth  => App\Http\Middleware\Authenticate::class  
   Limpia cachés: ddev artisan optimize:clear.

3) Throttle de /books  
   → Verifica con php artisan route:list que estén dentro de throttle:30,1.

---

## Licencia

Este proyecto se distribuye bajo la MIT License.  
Consulta el archivo LICENSE para más detalles.
