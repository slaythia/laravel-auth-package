<?php

namespace ec5\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminAuthenticate
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
        //dd(Auth::guard($guard)->jwtToken());
        // check if user is active
        if (!$user->isActive()) {
            // if not, log out and redirect to login page with error
            Auth::guard($guard)->logout();
            return redirect()->guest('login')->withErrors(['Sorry, your account has been disabled.']);
        }

        // redirect if not admin or super admin
        if (!$user->isAdmin() && !$user->isSuperAdmin()) {
            return redirect('/');
        }

        return $next($request);
    }
}
