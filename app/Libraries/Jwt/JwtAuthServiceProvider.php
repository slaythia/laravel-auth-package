<?php

namespace ec5\Libraries\Jwt;

use ec5\Repositories\Eloquent\User\UserRepository;
use Illuminate\Support\ServiceProvider;
use ec5\Libraries\Jwt\JwtUserProvider;
use ec5\Libraries\Jwt\JwtGuard;
use ec5\Libraries\Jwt\Jwt;
use ec5\Models\Users\User;
use Auth;

/**
 * Extend the Auth Guard service by adding a 'jwt' driver
 *
 * Class JwtAuthServiceProvider
 * @package ec5\Libraries\Jwt
 */
class JwtAuthServiceProvider extends ServiceProvider
{

    /**
     * Register any application authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        Auth::extend('jwt', function($app, $name, array $config)
        {
            // Create new Jwt User provider
            $provider = new JwtUserProvider($app['hash'], new User);
            // Pass this to the Jwt Guard
            $guard = new JwtGuard($provider, $app['request'], new Jwt(new UserRepository));

            // Set cookie jar
            if (method_exists($guard, 'setCookieJar')) {
                $guard->setCookieJar($app['cookie']);
            }

            // Refresh request
            if (method_exists($guard, 'setRequest')) {
                $guard->setRequest($app->refresh('request', $guard, 'setRequest'));
            }

            return $guard;

        });
    }

    public function register()
    {
        //
    }
}
