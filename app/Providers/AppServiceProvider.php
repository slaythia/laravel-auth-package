<?php

namespace ec5\Providers;

use Illuminate\Support\ServiceProvider;
use Auth;
use Config;
use Lang;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        $siteLogin = Lang::get('site.login');
        $siteAdmin = Lang::get('site.admin');
        $siteLogout = Lang::get('site.logout');

        // Set parameters shared with all views
        view()->share('siteLogin', $siteLogin);
        view()->share('siteAdmin', $siteAdmin);
        view()->share('siteLogout', $siteLogout);
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
