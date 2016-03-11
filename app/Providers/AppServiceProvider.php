<?php

namespace ec5\Providers;

use Illuminate\Support\ServiceProvider;
use Config;
use Lang;
use Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $navBarHi = trans('site.hi');
        $navBarLogIn = trans('site.login');
        $navBarLogOut = trans('site.logout');
        $navBarAdmin = trans('site.admin');

        view()->share('navBarHi', $navBarHi);
        view()->share('navBarLogIn', $navBarLogIn);
        view()->share('navBarLogOut', $navBarLogOut);
        view()->share('navBarAdmin', $navBarAdmin);
        view()->share('siteTitle', Config::get('app.site_title'));
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
