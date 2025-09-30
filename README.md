# ReadOn

📚 Aplicación web para registrar y reseñar libros, inspirada en Letterboxd pero orientada a lecturas.  
Proyecto personal de portfolio, construido con un stack sencillo y mantenible.

---

## 🚀 Stack

- **Backend**: Laravel 11 + Blade  
- **Base de datos**: PostgreSQL (vía DDEV)  
- **Frontend**: SCSS compilado con Vite (sin Tailwind ni Bootstrap)  
- **Tooling**: Node.js 20 (para compilación de assets con Vite)  
- **Entorno**: DDEV (Docker), PHP 8.2, nginx-fpm  

---

## ⚙️ Puesta en marcha (local)

Requisitos: DDEV (https://ddev.readthedocs.io/en/stable/)

```bash
# Clonar repositorio
git clone git@github.com:CristianSG2/ReadOn.git
cd ReadOn

# Iniciar entorno
ddev start

# Instalar dependencias PHP
ddev composer install

# Instalar dependencias JS
ddev npm install

# Generar clave de aplicación
ddev artisan key:generate

# Ejecutar migraciones
ddev artisan migrate

# Compilar assets (producción)
ddev npm run build
```

La aplicación quedará accesible en:  
https://readon.ddev.site

---

## 📂 Estructura actual

- resources/views/layouts/app.blade.php → Layout principal  
- resources/views/home.blade.php → Página de inicio (ejemplo)  
- resources/scss/app.scss → Estilos principales  

---

## ✅ Estado actual

- Base de Laravel funcionando con PostgreSQL (DDEV).  
- Vite configurado para SCSS.  
- Layout Blade y página `home` de prueba.  

---

## 🔮 Próximos pasos

- Autenticación manual (login/registro/logout).  
- Integración con la API de Google Books.  
- Modelo `logs` para registrar lecturas.  
