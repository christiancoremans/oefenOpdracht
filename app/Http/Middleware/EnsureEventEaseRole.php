<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — EnsureEventEaseRole middleware
|--------------------------------------------------------------------------
| Identical pattern to EnsureDevTalkRole but reads 'ee_role' instead of
| 'devtalk_role'. This keeps EventEase roles completely isolated from
| TechBazaar and DevTalk — being a DevTalk admin does NOT make you an
| EventEase admin.
|
| Registered in bootstrap/app.php as alias 'ee_role'.
| Used in routes: Route::middleware('ee_role:organizer,admin')
|--------------------------------------------------------------------------
*/
class EnsureEventEaseRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user() || ! in_array($request->user()->ee_role, $roles)) {
            abort(403, 'Access denied. Required EventEase role: ' . implode(' or ', $roles) . '.');
        }

        return $next($request);
    }
}
