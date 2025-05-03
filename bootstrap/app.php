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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\RedirectBasedOnRole::class,
        ]);
        $middleware->alias([
            'check.role' => \App\Http\Middleware\CheckRole::class,
        ]);
        // Autres configurations de middleware si nécessaire
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Configuration des exceptions si nécessaire
    })->create();
