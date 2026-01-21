# ReadOn

📚 Aplicación web para registrar, seguir y reseñar libros, inspirada en Letterboxd pero orientada a lecturas.
Proyecto personal de portfolio, enfocado en una arquitectura clara, UX cuidada y stack sencillo y mantenible.

---

## 🚀 Stack

- Backend: Laravel 11 + Blade
- Base de datos: PostgreSQL (vía DDEV)
- Frontend: SCSS compilado con Vite (sin Tailwind ni Bootstrap)
- Tooling: Node.js 20
- Entorno: DDEV (Docker), PHP 8.2

---

## ⚙️ Puesta en marcha (local)

Requisitos: DDEV (https://ddev.readthedocs.io/en/stable/)

git clone git@github.com:CristianSG2/ReadOn.git
cd ReadOn

ddev start
ddev composer install
ddev npm install
ddev artisan key:generate

Configurar en .env:
GOOGLE_BOOKS_API_KEY=tu_api_key

ddev artisan migrate
ddev npm run build
ddev launch

---

## 🎨 UX, visuales y temas

La aplicación incorpora un sistema de temas visuales intercambiables basado en data-theme y variables CSS.
El selector de tema está integrado en el header y la preferencia se guarda automáticamente en localStorage.

Temas disponibles:
- Lavender Dusk (por defecto)
- Rose Slate
- Blue Haze
- Forest Sage
- Mocha Cream
- Teal Breeze

El theming se aplica de forma global a:
- Navbar y enlaces activos
- Botones, inputs y selects
- Stat-cards y listas
- Badges de estado
- Empty states y formularios

---

## 📘 Logs de lectura

Funcionalidades:
- Búsqueda de libros mediante Google Books API
- Registro en lista personal
- Estados: Lista de deseos, Leyendo, Leído, Abandonado
- Rating (1–10) y reseñas
- Edición y eliminación de registros propios

Los estados se muestran con badges de color y labels en español.

---

## 👤 Perfil

La vista de perfil incluye:
- Total de lecturas
- Conteo por estado
- Media de rating
- Últimos registros añadidos

---

## 📑 Estado del proyecto

Actualmente el proyecto se encuentra en una fase avanzada a nivel funcional y visual, pero aún requiere algunos ajustes antes de considerarse cerrado como pieza de portfolio.

Implementado:
- Autenticación manual (login, registro, logout).
- Integración con Google Books API.
- CRUD completo de logs de lectura.
- Sistema de temas con selector persistente.
- UX refinada (badges de estado, empty states, transiciones y micro-interacciones).

Pendiente antes de portfolio:
- Ajustar la landing inicial.
- Corregir el guardado de libros desde la búsqueda a “Mis lecturas”.
- Revisar el tamaño y comportamiento de las imágenes en la vista de detalle del libro.
- Revisión final de accesibilidad y pequeños detalles visuales.
- Documentación final del proyecto.

---


## Licencia

MIT License
