<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Home
Route::get('/', function () {
    return view('welcome');
});

// ------------------------------
// Rutas públicas (solo invitados)
// ------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');

    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
});

// ------------------------------
// Rutas protegidas (solo logueados)
// ------------------------------
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // De momento, /me sigue como stub hasta crear la vista
    Route::get('/me', function () {
        $user = auth()->user();
        return response("Perfil (stub). Usuario: ".($user?->email ?? 'sin sesión'), 200);
    })->name('me');
});
