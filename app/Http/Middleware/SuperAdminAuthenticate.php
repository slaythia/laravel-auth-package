<?php

namespace ec5\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SuperAdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('login');
            }
        }

        $user = Auth::guard($guard)->user();

        // redirect if not super admin
        if (!$user->isSuperAdmin()) {
            return redirect('/');
        }

        return $next($request);
    }
}
