<?php

namespace ec5\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class BasicAuthenticate
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('login');
            }
        }

        // redirect if not admin or super admin
        if ($this->auth->user()->isAdmin() || $this->auth->user()->isSuperAdmin()) {
            return redirect('/');
        }

        return $next($request);
    }
}
