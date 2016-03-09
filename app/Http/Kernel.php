<?php

namespace ec5\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \ec5\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \ec5\Http\Middleware\VerifyCsrfToken::class,
        ],

        'api' => [
            'throttle:60,1'
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \ec5\Http\Middleware\Authenticate::class,
        'guest' => \ec5\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'auth.basic' => \ec5\Http\Middleware\BasicAuthenticate::class,
        'auth.admin' => \ec5\Http\Middleware\AdminAuthenticate::class,
        'auth.superadmin' => \ec5\Http\Middleware\SuperAdminAuthenticate::class
    ];
}
