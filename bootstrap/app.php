<?php

use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias 'guest' → redirect ke dashboard jika sudah login
        $middleware->alias([
            'guest'    => RedirectIfAuthenticated::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'role'     => \App\Http\Middleware\CheckRole::class,
        ]);

        // Sanctum stateful domains untuk SPA (opsional, siap untuk Increment 3)
        $middleware->statefulApi();

        // Kecualikan webhook Midtrans dari CSRF verification
        // (server Midtrans memanggil endpoint ini tanpa CSRF token)
        $middleware->validateCsrfTokens(except: [
            'api/donasi/notification',
            'api/donasi/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();

