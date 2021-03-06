<?php

namespace ec5\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;

class AdminAuthenticate
{

    /*
    |--------------------------------------------------------------------------
    | AdminAuthenticate
    |--------------------------------------------------------------------------
    |
    | This middleware checks the user is a logged in 'admin' before being allowed to proceed
    |
    */

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

        // Check if user is active
        if (!$user->isActive()) {
            // if not, log out and redirect to login page with error
            Auth::guard($guard)->logout();
            return redirect()->guest('login')->withErrors(['Sorry, your account has been disabled.']);
        }

        // Redirect if not admin or super admin
        if (!$user->isAdmin() && !$user->isSuperAdmin()) {
            return redirect('/');
        }

        return $next($request);
    }
}
