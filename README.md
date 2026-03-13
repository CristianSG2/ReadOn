# ReadOn

📚 Aplicación web para registrar, seguir y reseñar libros, inspirada en Letterboxd pero orientada a lecturas.
Proyecto personal de portfolio, enfocado en una arquitectura clara, UX cuidada y un stack sencillo y mantenible.

---

## 🚀 Stack

- Backend: Laravel 11 + Blade
- Base de datos: PostgreSQL
- Frontend: SCSS compilado con Vite
  - SCSS como única fuente de verdad visual, estructurado por secciones
  - Sin Tailwind en vistas — clases semánticas propias en todas las plantillas
  - Sistema de tokens CSS basado en Catppuccin (Mocha dark / Latte light)
- Tooling: Node.js 20 (Vite para assets)
- Entorno: DDEV (Docker), PHP 8.2, nginx-fpm

---

## ⚙️ Puesta en marcha (local)

Requisitos: DDEV → https://ddev.readthedocs.io/en/stable/

git clone git@github.com:CristianSG2/ReadOn.git
cd ReadOn
ddev start
ddev composer install
ddev npm install
ddev artisan key:generate

Añadir en .env:
GOOGLE_BOOKS_API_KEY=tu_api_key

ddev artisan migrate
ddev npm run build
ddev launch

---

## 🔐 Autenticación

Autenticación manual (sin Breeze):
- Login, registro y logout propios
- Middleware auth
- CSRF, regeneración de sesión y throttle de login
- Redirección post-login a /me

---

## 📚 Logs de lectura

- Búsqueda vía Google Books API
- Guardado de libros en lista personal
- Estados: Wishlist, Leyendo, Leído, Abandonado
- Rating (1–10) y reseñas
- Edición y borrado de registros propios

---

## 👤 Perfil

Vista /me con:
- Estadísticas de lectura
- Media de ratings
- Últimos registros
- Acceso a listado completo

---

## 🎨 UX y temas

Sistema de temas basado en la paleta Catppuccin:
- **Mocha** (oscuro) — activo por defecto
- **Latte** (claro) — seleccionable desde el nav

El switch persiste entre sesiones vía `localStorage`. El tema se aplica antes del primer render para evitar flash. Todos los colores consumen tokens CSS (`--accent`, `--bg`, `--surface`, `--text`, etc.); no hay valores hardcoded fuera del bloque de tokens.

---

## 📑 Estado del proyecto

v0.2.0 — estable y funcional tras refactor visual completo:
- Flujo completo de lectura operativo
- Landing pública mínima funcional
- Login y registro unificados visualmente
- SCSS limpio: sin duplicados, sin código muerto, sin clases de utilidad inline
- Bugs corregidos: variables CSS indefinidas, errores de validación invisibles, colores que ignoraban el tema

### Próximos pasos
- Despliegue público
- Identidad visual ligera

---

## 🧰 Scripts útiles

ddev npm run dev
ddev npm run build
ddev artisan optimize:clear

---

Licencia MIT
