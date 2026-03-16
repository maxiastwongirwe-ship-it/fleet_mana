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
        // Add your custom middleware aliases here
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            // You can add more aliases later, e.g.:
             'role' => \App\Http\Middleware\RoleMiddleware::class,

            // 'verified.phone' => \Some\Other\Middleware::class,
        ]);

        // Optional: Append global middleware to the 'web' group if needed
        // $middleware->web(append: [
        //     \App\Http\Middleware\SomeGlobalMiddleware::class,
        // ]);

        // Optional: Redirect users to login when unauthenticated
        $middleware->redirectUsersTo('/login');

        // Optional: Redirect guests away from protected routes
        $middleware->redirectGuestsTo('/login');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // You can customize exception handling here later if needed
        // For example:
        // $exceptions->reportable(function (Throwable $e) {
        //     // Log to Sentry, etc.
        // });
    })->create();