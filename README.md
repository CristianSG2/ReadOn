# ReadOn

📚 Aplicación web para registrar, seguir y reseñar libros, inspirada en Letterboxd pero orientada a lecturas.
Proyecto personal de portfolio, enfocado en una arquitectura clara, UX cuidada y un stack sencillo y mantenible.

---

## 🚀 Stack

- Backend: Laravel 11 + Blade
- Base de datos: PostgreSQL
- Frontend: SCSS compilado con Vite
  - SCSS como base y fuente de verdad visual
  - Uso puntual de utilidades Tailwind en algunos ajustes rápidos de layout
  - Objetivo: consolidar todo en SCSS progresivamente
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

Sistema de temas con variables CSS y selector persistente.
Temas incluidos:
Lavender Dusk, Rose Slate, Blue Haze, Forest Sage, Mocha Cream, Teal Breeze

---

## 📑 Estado del proyecto

Estado estable y funcional:
- Flujo completo de lectura operativo
- Landing pública mínima funcional
- Login y registro unificados visualmente
- Sistema de temas aplicado globalmente

### Próximos pasos
- Pulido final de UX
- Consolidación visual
- Identidad visual ligera
- Despliegue público

---

## 🧰 Scripts útiles

ddev npm run dev
ddev npm run build
ddev artisan optimize:clear

---

Licencia MIT
