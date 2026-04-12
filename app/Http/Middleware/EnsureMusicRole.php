<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — EnsureMusicRole middleware
|--------------------------------------------------------------------------
| Usage in routes:
|   Route::middleware('music_role:admin')
|   Route::middleware('music_role:admin,user')
|
| The middleware reads roles from the route definition via explode(',').
| auth()->user() is already guaranteed by the outer 'auth' middleware.
|
| Returns HTTP 403 Forbidden if the user's music_role is not in the list.
|--------------------------------------------------------------------------
*/

class EnsureMusicRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! in_array($request->user()?->music_role, $roles)) {
            abort(403, 'Access denied for MusicHub.');
        }

        return $next($request);
    }
}
