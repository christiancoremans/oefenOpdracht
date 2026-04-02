<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Custom middleware
|--------------------------------------------------------------------------
| Middleware sits BETWEEN the request and the controller.
| It runs before (or after) every request that uses it.
|
| HOW TO CREATE IN AN EXAM:
|   php artisan make:middleware EnsureRole
|   → Creates this file, then fill in handle()
|
| HOW TO REGISTER (Laravel 11+):
|   In bootstrap/app.php inside ->withMiddleware():
|     $middleware->alias(['role' => EnsureRole::class]);
|
| HOW TO USE IN ROUTES:
|   Route::middleware('role:admin')->...
|   Route::middleware('role:buyer,admin')->...
|
| string ...$roles (variadic)
|   → Receives all role arguments as an array.
|   → Route::middleware('role:buyer,admin') passes ['buyer', 'admin']
|
| abort(403) → Returns "403 Forbidden" HTTP response.
|   → The browser sees an error page; the controller is never reached.
|--------------------------------------------------------------------------
*/

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user() || ! in_array($request->user()->role, $roles)) {
            abort(403, 'Access denied. Required role: ' . implode(' or ', $roles) . '.');
        }

        return $next($request);
    }
}
