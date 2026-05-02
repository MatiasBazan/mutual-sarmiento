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
        $middleware->alias([
            'admin'       => \App\Http\Middleware\EsAdmin::class,
            'no-turnero'  => \App\Http\Middleware\NoEsTurnero::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            '/webhook/whatsapp',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
