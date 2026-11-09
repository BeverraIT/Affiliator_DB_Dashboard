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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(['kora.token' => \App\Http\Middleware\KoraApiToken::class]);
        // API KORA tidak memakai sesi/formulir: dijaga token bearer.
        $middleware->validateCsrfTokens(except: ['kora-api/*']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();