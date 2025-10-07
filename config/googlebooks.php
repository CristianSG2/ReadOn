<?php

return [
    // URL base oficial de Google Books
    'base_url' => 'https://www.googleapis.com/books/v1',

    // Timeout de las llamadas HTTP (segundos)
    'timeout' => 5.0,

    // Clave desde el .env (no commiteada)
    'key' => env('GOOGLE_BOOKS_API_KEY'),
    
    // Tiempo de caché (minutos)
    'cache_minutes' => 15,
];
