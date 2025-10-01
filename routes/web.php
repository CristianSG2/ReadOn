<?php

use Illuminate\Support\Facades\Route;

// Home existente
Route::get('/', function () {
    return view('welcome');
});

// ------------------------------
// Rutas públicas (solo invitados)
// ------------------------------
Route::middleware('guest')->group(function () {

    // GET /login (stub temporal)
    Route::get('/login', function () {
        return response("Login form (stub)", 200);
    })->name('login');

    // POST /login (stub temporal)
    Route::post('/login', function () {
        return response("LOGIN no implementado aún", 501);
    })->name('login.post');

    // GET /register (stub temporal)
    Route::get('/register', function () {
        return response("Register form (stub)", 200);
    })->name('register');

    // POST /register (stub temporal)
    Route::post('/register', function () {
        return response("REGISTER no implementado aún", 501);
    })->name('register.post');
});

// ------------------------------
// Rutas protegidas (solo logueados)
// ------------------------------
Route::middleware('auth')->group(function () {

    // POST /logout (stub temporal)
    Route::post('/logout', function () {
        return response("LOGOUT no implementado aún", 501);
    })->name('logout');

    // GET /me (stub temporal)
    Route::get('/me', function () {
        $user = auth()->user();
        return response("Perfil (stub). Usuario: ".($user?->email ?? 'sin sesión'), 200);
    })->name('me');
});
