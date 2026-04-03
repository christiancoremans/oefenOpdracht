<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — EnsureDevTalkRole middleware
|--------------------------------------------------------------------------
| Identical pattern to EnsureRole (TechBazaar) but reads 'devtalk_role'
| instead of 'role'. This keeps the two projects completely independent —
| being a TechBazaar admin does NOT make you a forum admin.
|
| Registered in bootstrap/app.php as alias 'devtalk_role'.
| Used in routes: Route::middleware('devtalk_role:moderator,admin')
|--------------------------------------------------------------------------
*/
class EnsureDevTalkRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user() || ! in_array($request->user()->devtalk_role, $roles)) {
            abort(403, 'Access denied. Required DevTalk role: ' . implode(' or ', $roles) . '.');
        }

        return $next($request);
    }
}
