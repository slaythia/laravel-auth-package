<?php

namespace ec5\Libraries\Jwt;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use ec5\Repositories\Eloquent\User\UserRepository;
use ec5\Libraries\Jwt\JwtUserProvider;
use ec5\Libraries\Jwt\JwtGuard;
use ec5\Libraries\Jwt\Jwt;
use ec5\Models\Users\User;

/**
 * Extend the Auth Guard service by adding a 'jwt' driver
 *
 * Class JwtAuthServiceProvider
 * @package ec5\Libraries\Jwt
 */
class JwtAuthServiceProvider extends ServiceProvider
{

    public function register()
    {
        //
    }

    /**
     * Register any application authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['auth']->extend('jwt', function()
        {
            // Create new Jwt User provider
            $provider = new JwtUserProvider($this->app['hash'], new User);
            // Pass this to the Jwt Guard
            $guard = new JwtGuard($provider, $this->app['request'], new Jwt(new UserRepository));

            // Set cookie jar
            if (method_exists($guard, 'setCookieJar')) {
                $guard->setCookieJar($this->app['cookie']);
            }

            // Refresh request
            if (method_exists($guard, 'setRequest')) {
                $guard->setRequest($this->app->refresh('request', $guard, 'setRequest'));
            }

            return $guard;

        });
    }
}
