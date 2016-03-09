<?php

namespace ec5\Libraries\Jwt;

use RuntimeException;
use Illuminate\Support\Str;
use ec5\Libraries\Jwt\JwtUserProvider as UserProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Cookie\QueueingFactory as CookieJar;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use ec5\Libraries\Jwt\Jwt;
use Cookie;
use Config;

class JwtGuard implements Guard
{
    /**
     * The currently authenticated user.
     *
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    protected $user;

    /**
     * The user we last attempted to retrieve.
     *
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    protected $lastAttempted;

    /**
     * The user provider implementation.
     *
     * @var \Illuminate\Contracts\Auth\UserProvider
     */
    protected $provider;

    /**
     * The Illuminate cookie creator service.
     *
     * @var \Illuminate\Contracts\Cookie\QueueingFactory
     */
    protected $cookie;

    /**
     * The request instance.
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * Indicates if the logout method has been called.
     *
     * @var bool
     */
    protected $loggedOut = false;

    /**
     * The JWT class
     *
     * @var
     */
    protected $jwt;

    /**
     * The JWT token
     *
     * @var
     */
    protected $jwtToken;

    /**
     * The name of the field on the request containing the API token.
     *
     * @var string
     */
    protected $inputKey;

    /**
     * The name of the token "column" in persistent storage.
     *
     * @var string
     */
    protected $storageKey;

    /**
     * Expiration time for the JWT token
     * @var int
     */
    protected $expirationTime = 60;

    /**
     * External request url that should be checked for
     * a JWT token differently to an internal url
     *
     * @var string
     */
    protected $externalRequestUrl = '/api/json';

    /**
     * Create a new authentication guard.
     *
     * JwtGuard constructor.
     * @param UserProvider $provider
     * @param Request|null $request
     * @param \ec5\Libraries\Jwt\Jwt $jwt
     */
    public function __construct(UserProvider $provider, Request $request = null, Jwt $jwt)
    {
        $this->request = $request;
        $this->provider = $provider;
        $this->jwt = $jwt;
        $this->inputKey = 'jwt';
        $this->storageKey = 'api_token';
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        return !is_null($this->user());
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest()
    {
        return !$this->check();
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if ($this->loggedOut) {
            return;
        }

        // If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
        if (!is_null($this->user)) {
            return $this->user;
        }

        // Retrieve the jwt token for the request
        $this->jwtToken = $this->getTokenForRequest();

        if ($this->jwtToken) {

            // Retrieve the claim from the jwt
            $claim = $this->jwt->verifyToken($this->jwtToken, true);

            if ($claim) {
                // Retrieve api_token value
                $jtiToken = $claim->jti;

                // Retrieve the user
                $this->user = $this->provider->retrieveByCredentials(
                    [$this->storageKey => $jtiToken]
                );
            }
        }

        return $this->user;
    }

    /**
     * @return mixed
     */
    public function jwtToken()
    {
        return $this->jwtToken;
    }

    /**
     * Get the token for the current request.
     * Try to retrieve from request input, cookie or auth bearer
     *
     * @return string
     */
    protected function getTokenForRequest()
    {
        // Check if external or internal api request
        if (preg_match('#^' . $this->externalRequestUrl . '#', $this->request->getPathInfo())) {

            // If external, check for jwt in input or authorization bearer header
            $token = $this->request->input($this->inputKey);

            if (empty($token)) {
                $token = $this->request->bearerToken();
            }

        } else {
            // If internal, check for jwt in cookie only
            $token = $this->request->cookie($this->inputKey);
        }

        return $token;
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|null
     */
    public function id()
    {
        if ($this->loggedOut) {
            return;
        }

        $id = $this->session->get($this->getName(), $this->getRecallerId());

        if (is_null($id) && $this->user()) {
            $id = $this->user()->getAuthIdentifier();
        }

        return $id;
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return $this->attempt($credentials, false, false);
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param  array $credentials
     * @param  bool $setCookie
     * @param  bool $login
     * @return bool
     */
    public function attempt(array $credentials = [], $setCookie = true, $login = true)
    {
        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);

        // If an implementation of UserInterface was returned, we'll ask the provider
        // to validate the user against the given credentials, and if they are in
        // fact valid we'll log the users into the application and return true.
        if ($this->hasValidCredentials($user, $credentials)) {
            if ($login) {
                $this->login($user, $setCookie);
            }

            return true;
        }

        return false;
    }

    /**
     * Determine if the user matches the credentials.
     *
     * @param  mixed $user
     * @param  array $credentials
     * @return bool
     */
    protected function hasValidCredentials($user, $credentials)
    {
        return !is_null($user) && $this->provider->validateCredentials($user, $credentials);
    }

    /**
     * Log a user into the application.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  bool $setCookie
     * @return void
     */
    public function login(UserContract $user, $setCookie = true)
    {
        $jwtConfig = Config::get('auth.jwt');

        // Generate new api_token
        $token = $this->jwt->generateApiToken($user);

        // Save api token
        $this->provider->updateApiToken($user, $token);

        // Generate jwt token
        $this->jwtToken = $this->jwt->generateToken($token);

        if ($setCookie) {
            // Add jwt cookie to queue
            $this->cookie->queue(cookie($this->inputKey, $this->jwtToken, $jwtConfig['expire']));
        }

        $this->setUser($user);

    }

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout()
    {
        // Remove the api token from user
        if ($user = $this->user()) {
            $this->provider->updateApiToken($user, '');
        }

        // Clear cookie data
        $this->clearUserDataFromStorage();

        // Null user
        $this->user = null;

        $this->loggedOut = true;
    }

    /**
     * Remove the user data from the cookies.
     *
     * @return void
     */
    protected function clearUserDataFromStorage()
    {
        $this->cookie->queue(Cookie::forget($this->inputKey));
    }

    /**
     * Get the cookie creator instance used by the guard.
     *
     * @return \Illuminate\Contracts\Cookie\QueueingFactory
     *
     * @throws \RuntimeException
     */
    public function getCookieJar()
    {
        if (!isset($this->cookie)) {
            throw new RuntimeException('Cookie jar has not been set.');
        }

        return $this->cookie;
    }

    /**
     * Set the cookie creator instance used by the guard.
     *
     * @param  \Illuminate\Contracts\Cookie\QueueingFactory $cookie
     * @return void
     */
    public function setCookieJar(CookieJar $cookie)
    {
        $this->cookie = $cookie;
    }

    /**
     * Get the user provider used by the guard.
     *
     * @return \Illuminate\Contracts\Auth\UserProvider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param JwtUserProvider $provider
     */
    public function setProvider(UserProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Return the currently cached user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the current user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @return void
     */
    public function setUser(UserContract $user)
    {
        $this->user = $user;

        $this->loggedOut = false;
    }

    /**
     * Get the current request instance.
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request ?: Request::createFromGlobals();
    }

    /**
     * Set the current request instance.
     *
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get the last user we attempted to authenticate.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function getLastAttempted()
    {
        return $this->lastAttempted;
    }

}
