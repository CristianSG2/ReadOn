<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ReadingLogController;


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

// Rutas de logs de lectura (solo para usuarios autenticados)
Route::middleware('auth')->group(function () {
    Route::post('/reading-logs', [ReadingLogController::class, 'store'])->name('reading-logs.store');
    Route::get('/reading-logs',  [ReadingLogController::class, 'index'])->name('reading-logs.index'); // listado
    
    Route::patch('/reading-logs/{readingLog}', [ReadingLogController::class, 'update'])
    ->name('reading-logs.update'); // estado
    Route::patch('/reading-logs/{readingLog}/rating', [ReadingLogController::class, 'updateRating'])
    ->name('reading-logs.rating'); // rating
    Route::patch('/reading-logs/{readingLog}/review', [ReadingLogController::class, 'updateReview'])
    ->name('reading-logs.review');// reseña
    Route::delete('/reading-logs/{readingLog}', [ReadingLogController::class, 'destroy'])
    ->name('reading-logs.destroy');// eliminar

});
