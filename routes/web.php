<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookController;

// Home
Route::get('/', function () {
    return view('welcome');
});

// ------------------------------
// Rutas públicas (solo invitados)
// ------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

    // Aquí añadimos el throttle SOLO al POST /login
    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:login')
        ->name('login.post');

    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
});

// ------------------------------
// Rutas protegidas (solo logueados)
// ------------------------------
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/me', function () {
        return view('me');   
    })->name('me');
});

// ------------------------------
// Rutas Books (solo logueados)
// ------------------------------

Route::get('/books', [BookController::class, 'index'])->name('books.index');   // búsqueda/listado
Route::get('/books/{id}', [BookController::class, 'show'])->name('books.show'); // detalle

// Rate limit para proteger la cuota de Google Books.
Route::middleware('throttle:30,1')->group(function () { // 30 solicitudes por minuto y por IP SOLO para /books.
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/{id}', [BookController::class, 'show'])->name('books.show');
});