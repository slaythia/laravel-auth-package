<?php

namespace ec5\Http\Controllers\Api\Json\Auth;

use ec5\Http\Controllers\Api\Json\ApiResponse;
use ec5\Repositories\Eloquent\User\UserRepository;
use ec5\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ec5\Libraries\Jwt\Jwt;
use ec5\Libraries\Ldap\Ldap;
use Auth;
use Config;
use Exception;
use Socialite;
use URL;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Api Authentication Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the authentication of api requests
    | by verifying the user, given the supplied credentials,
    | generating a JWT token for the user, supplied in Authorization header
    |
    */

    /**
     * @var UserRepository Object $userRepository
     */
    protected $userRepository;

    /**
     * @var Jwt
     */
    protected $jwt;

    /**
     * @var
     */
    protected $authMethods;

    /**
     * Create a new api auth controller instance.
     *
     * @param UserRepository $userRepository
     * @param Jwt $jwt
     */
    public function __construct(UserRepository $userRepository, Jwt $jwt)
    {
        $this->userRepository = $userRepository;
        $this->jwt = $jwt;

        // Determine which authentication method is available
        $this->authMethods = Config::get('auth.auth_methods');

    }

    /**
     * @param ApiResponse $apiResponse
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLogin(ApiResponse $apiResponse)
    {
        $authIds = [];
        // If google is an auth method, supply our Client ID
        if (in_array('google', $this->authMethods)) {
            $providerKey = \Config::get('services.google_api');
            $authIds['google']['CLIENT_ID'] = $providerKey['client_id'];
        }

        // return response
        $apiResponse->data = [
            'type' => 'login',
            'login' => [
                'methods' => $this->authMethods,
                'auth_ids' => $authIds
            ]
        ];
        return $apiResponse->toJsonResponse(200);
    }

    /**
     * Handle a login request to the application.
     *
     * @param Request $request
     * @param ApiResponse $apiResponse
     * @return \Illuminate\Http\JsonResponse
     */
    public function postLogin(Request $request, ApiResponse $apiResponse)
    {

        if (in_array('local', $this->authMethods)) {

            $credentials = $request->only('username', 'password');

            // Verify user, without setting cookie
            if (Auth::attempt(['email' => $credentials['username'], 'password' => $credentials['password']], false, true)) {

                $apiResponse->data = [];
                // Return response
                return $apiResponse->toJsonResponse(200, 'data', 0, ['Authorization' => 'Bearer ' . Auth::jwtToken()]);
            }

            if ($this->jwt->hasErrors()) {
                return $apiResponse->errorResponse(499, $this->jwt->errors());
            }

            $error['api/json/login'] = ['ec5_12'];
            return $apiResponse->errorResponse(499, $error);
        }
        // Auth method not allowed
        $error['api/json/login'] = ['ec5_55'];
        return $apiResponse->errorResponse(499, $error);

    }

    /**
     * Handle an ldap login api request to the application.
     *
     * @param Request $request
     * @param ApiResponse $apiResponse
     * @return \Illuminate\Http\JsonResponse
     */
    public function postLdapLogin(Request $request, ApiResponse $apiResponse)
    {

        // Check this auth method is allowed
        if (in_array('ldap', $this->authMethods)) {
            // Attempt to find the ldap user
            try {

                // If we can't connect
                if (Ldap::hasErrors()) {
                    return $apiResponse->errorResponse(499, Ldap::errors());
                }

                $ldapUser = Ldap::retrieveByCredentials($request->only('username', 'password'));

                // If we found and verified the user, generate JWT token
                if ($ldapUser) {

                    $user = $this->userRepository->findOrCreateLdapUser($ldapUser);

                    // Log user in, without setting cookie
                    Auth::login($user, false);
                    $apiResponse->data = [];
                    // Return response
                    return $apiResponse->toJsonResponse(200, 'data', 0, ['Authorization' => 'Bearer ' . Auth::jwtToken()]);

                }

            } catch (Exception $e) {
                // If any exceptions, return error response: could not authenticate

            }

            // Check if any JWT specific errors
            if ($this->jwt->hasErrors()) {
                return $apiResponse->errorResponse(499, $this->jwt->errors());
            }

            $error['api/json/login'] = ['ec5_33'];
            return $apiResponse->errorResponse(499, $error);
        }
        // Auth method not allowed
        $error['api/json/login'] = ['ec5_55'];
        return $apiResponse->errorResponse(499, $error);

    }

    /**
     * Accepts access code and creates google social user
     * Returning jwt in response header
     *
     * @param ApiResponse $apiResponse
     * @return \Illuminate\Http\JsonResponse
     */
    public function authGoogleUser(ApiResponse $apiResponse)
    {

        // Check this auth method is allowed
        if (in_array('google', $this->authMethods)) {
            // Attempt to find the google user
            try {
                $providerKey = \Config::get('services.google_api');

                // We want stateless here, as using jwt
                // Build the custom provider driver based on google driver and load the user
                $providerUser = Socialite::buildProvider('Laravel\Socialite\Two\GoogleProvider', $providerKey)->stateless()->user();

                $socialUser = $this->userRepository->findOrCreateSocialUser('google', $providerUser);

                // Check user exists and is active
                if ($socialUser) {

                    // Log user in, without setting cookie
                    Auth::login($socialUser, false);
                    $apiResponse->data = [];
                    // Return response
                    return $apiResponse->toJsonResponse(200, 'data', 0, ['Authorization' => 'Bearer ' . Auth::jwtToken()]);

                }
            } catch (Exception $e) {
                // If any exceptions, return error response: could not authenticate

            }

            // Check if any JWT specific errors
            if ($this->jwt->hasErrors()) {
                return $apiResponse->errorResponse(499, $this->jwt->errors());
            }

            $error['api/json/login'] = ['ec5_32'];
            return $apiResponse->errorResponse(499, $error);
        }
        // Auth method not allowed
        $error['api/json/login'] = ['ec5_55'];
        return $apiResponse->errorResponse(499, $error);

    }

}
