<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Alias de middlewares (Laravel 11)
        $middleware->alias([
            'auth'    => \App\Http\Middleware\Authenticate::class,
            'guest'   => \App\Http\Middleware\RedirectIfAuthenticated::class, // ← ESTE es el cambio clave
            'throttle'=> \Illuminate\Routing\Middleware\ThrottleRequests::class,
            // (deja el resto de alias que ya tengas)
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
