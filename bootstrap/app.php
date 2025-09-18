<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\StudentAuthMiddleware;
use App\Http\Middleware\AdminAuthMiddleware;
use App\Http\Middleware\StudentRoleMiddleware;
use App\Http\Middleware\AdminRoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'student.auth' => StudentAuthMiddleware::class,
            'admin.auth' => AdminAuthMiddleware::class,
            'student.role' => StudentRoleMiddleware::class,
            'admin.role' => AdminRoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
