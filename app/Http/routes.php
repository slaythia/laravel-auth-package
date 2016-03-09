<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

// Internal web routes
Route::group(['middleware' => ['web']], function () {

    Route::get('/', 'HomeController@index');

    // Authentication routes
    Route::group(['prefix' => 'login'], function () {

        Route::get('/', 'Auth\AuthController@getLogin');
        Route::get('/admin', 'Auth\AuthController@getAdminLogin');
        Route::post('/', 'Auth\AuthController@postLogin');
        Route::post('ldap', 'Auth\AuthController@postLdapLogin');

    });

    Route::get('logout', 'Auth\AuthController@getLogout');


    // Password reset
    Route::get('password', 'Auth\PasswordController@getEmail');
    Route::post('password', 'Auth\PasswordController@postEmail');
    Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
    Route::post('password/reset', 'Auth\PasswordController@postReset');


    // Google+ Authentication routes
    Route::get('redirect/{provider}', 'Auth\AuthController@redirectToProvider');
    Route::get('handle/{provider}', 'Auth\AuthController@handleProviderCallback');


    // Admin area and functions
    Route::group(['prefix' => 'admin'], function () {

        Route::get('/', 'Admin\AdminController@index');
        Route::post('update-user-server-role', 'Admin\ManageUsersController@updateUserServerRole');
        Route::post('update-user-state', 'Admin\ManageUsersController@updateUserState');
        Route::post('register-by-admin', 'Admin\ManageUsersController@postRegisterByAdmin');

    });

    // User search
    Route::get('search-by-email', 'Admin\ManageUsersController@searchByEmail');


    // Internal JSON API routes
    Route::group(['prefix' => 'api/internal/json'], function () {

        Route::get('example', 'Api\Json\ExampleController@index');
        Route::post('example', 'Api\Json\ExampleController@index');

    });

});


// External JSON API routes
Route::group(['prefix' => 'api/json', 'middleware' => ['api']], function () {

    // Auth
    Route::get('login', 'Api\Json\Auth\AuthController@getLogin');
    Route::post('login/local', 'Api\Json\Auth\AuthController@postLogin');
    Route::post('login/ldap', 'Api\Json\Auth\AuthController@postLdapLogin');
    Route::post('handle/google', 'Api\Json\Auth\AuthController@authGoogleUser');

    Route::get('example', 'Api\Json\ExampleController@index');
    Route::post('example', 'Api\Json\ExampleController@index');

});


