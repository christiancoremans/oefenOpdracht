<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — EnsureDriveSmartRole middleware
|--------------------------------------------------------------------------
| Identical pattern to EnsureEventEaseRole but reads 'ds_role'.
| Registered in bootstrap/app.php as alias 'ds_role'.
|
| Usage in routes:
|   Route::middleware('ds_role:instructor,admin')  → instructor OR admin
|   Route::middleware('ds_role:admin')             → admin only
|--------------------------------------------------------------------------
*/
class EnsureDriveSmartRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user() || ! in_array($request->user()->ds_role, $roles)) {
            abort(403, 'Access denied. Required DriveSmart role: ' . implode(' or ', $roles) . '.');
        }

        return $next($request);
    }
}
