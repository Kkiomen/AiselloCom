<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Dodaj middleware SetLocale do web middleware stack
        // Add SetLocale middleware to web middleware stack
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        // Rejestracja custom middleware z aliasami
        $middleware->alias([
            'auth.api.key' => \App\Http\Middleware\AuthenticateApiKey::class,
            'api.rate.limit' => \App\Http\Middleware\CheckApiRateLimit::class,
            'admin' => \App\Http\Middleware\IsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
