<?php

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
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
        // Exclude ALL api routes from CSRF
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'api/register',
            'api/login',
            'api/logout',
            'api/courses',
            'api/courses/*',
            'api/tasks',
            'api/tasks/*',
            'api/notes',
            'api/notes/*',
            'api/reminders',
            'api/reminders/*',
            'api/user',
            'api/profile',
            'api/profile/*',
            'api/change-password',
            'api/account',
            'api/preferences',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function (Request $request) {
            return $request->is('api/*') || $request->expectsJson();
        });
    })->create();