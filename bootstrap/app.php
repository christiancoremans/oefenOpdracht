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
        /*
        |----------------------------------------------------------------------
        | EXAM STUDY NOTE — Middleware alias registration (Laravel 11+)
        |----------------------------------------------------------------------
        | $middleware->alias([...])
        |   → Gives your middleware a short name usable in route definitions.
        |   → Without this alias you'd have to write the full class name:
        |       Route::middleware(\App\Http\Middleware\EnsureRole::class)
        |   → With the alias:
        |       Route::middleware('role:admin')
        |----------------------------------------------------------------------
        */
        $middleware->alias([
            'role'         => \App\Http\Middleware\EnsureRole::class,
            'devtalk_role' => \App\Http\Middleware\EnsureDevTalkRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
