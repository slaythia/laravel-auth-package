<?php

namespace ec5\Http\Controllers\Auth;

use ec5\Models\Users\User;
use Validator;
use ec5\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use ec5\Repositories\Eloquent\User\UserRepository;
use ec5\Libraries\Jwt\Jwt;
use Config;
use View;
use Illuminate\Http\Request;
use Exception;
use Socialite;
use Auth;
use Ldap;
use ec5\Http\Validation\Auth\RuleLogin as LoginValidator;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * @var string redirect path
     */
    protected $redirectPath = '/';

    /**
     * @var string superadmin/admin redirect path
     */
    protected $adminRedirectPath = '/admin';

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var
     */
    protected $authMethods = [];

    /**
     * @var Jwt
     */
    protected $jwt;

    /**
     * @var int
     */
    protected $expirationTime = 60;

    /**
     * Create a new authentication controller instance, injecting UserRepository instance
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository, Jwt $jwt)
    {
        // Set middleware to guest, exempting getLogout as this requires a logged in user
        $this->middleware('guest', ['except' => ['getLogout']]);
        $this->userRepository = $userRepository;
        $this->jwt = $jwt;

        // Determine which authentication methods are available
        $this->authMethods = Config::get('auth.auth_methods');

        // Always pass the authentication method variables to the login view
        View::composer('auth.login', function ($view) {
            $view->with('authMethods', $this->authMethods);
        });

    }

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogin()
    {
        return view('auth.login');
    }

    /**
     * Show the application login form for server admins.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAdminLogin()
    {
        return view('admin.login');
    }

    /**
     * Handle a local login request to the application.
     *
     * @param Request $request
     * @param LoginValidator $validator
     * @return $this|\Illuminate\Http\Response
     */
    public function postLogin(Request $request, LoginValidator $validator)
    {
        // Check this auth method is allowed
        if (in_array('local', $this->authMethods)) {

            $input = $request->all();

            $validator->validate($input);
            if ($validator->hasErrors()) {
                // redirect back if errors
                return redirect()->back()->withErrors($validator->errors());
            }

            // Throttle local login attempts
            $throttles = $this->isUsingThrottlesLoginsTrait();

            // Send a lock out response if user has made too many login attempts;
            // response is set in Lang auth.throttle
            if ($throttles && $this->hasTooManyLoginAttempts($request)) {
                return view('auth.login')->withErrors(['ec5_37']);
            }

            // Check credentials ie email, password and active state
            $credentials = array(
                'email' => $input['email'],
                'password' => $input['password'],
                'state' => 'active'
            );

            // Attempt to log the user in
            if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
                return $this->handleUserWasAuthenticated($request, $throttles);
            }

            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            if ($throttles) {
                $this->incrementLoginAttempts($request);
            }

            return view('auth.login')
                ->withInput($request->only($this->loginUsername(), 'remember'))
                ->withErrors(['ec5_36']);
        }

        // Auth method not allowed
        return view('auth.login')->withErrors(['ec5_55']);

    }

    /**
     * Function for redirecting to provider specific auth url
     *
     * @param $provider
     * @return $this
     */
    public function redirectToProvider($provider)
    {
        // Check this auth method is allowed
        if (in_array($provider, $this->authMethods)) {
            // Retrieve provider config details
            $providerKey = \Config::get('services.' . $provider);

            if (empty($providerKey)) {
                return view('auth.login')->withErrors(['ec5_38']);
            }

            return Socialite::with($provider)->redirect();
        }
        // Auth method not allowed
        return view('auth.login')
            ->withErrors(['ec5_55']);

    }

    /**
     * Function for handling the provider specific auth callback
     *
     * @param $provider
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function handleProviderCallback($provider)
    {

        try {
            // Load the provider user
            $providerUser = Socialite::with($provider)->user();

            // Check user exists and is active
            $socialUser = $this->userRepository->findOrCreateSocialUser($provider, $providerUser);

            if ($socialUser) {
                // Login user
                Auth::login($socialUser, true);

                // Redirect to home or admin page, depending on server role
                if ($socialUser->role == 'basic') {
                    return view('app');
                } else {
                    return redirect($this->adminRedirectPath);
                }

            }

        } catch (Exception $e) {

            // Catch any exceptions here

            // Return login failed error
            return view('auth.login')->withErrors(['ec5_31']);

        }
        // Return login failed error
        return view('auth.login')->withErrors(['ec5_32']);

    }

    /**
     * Handle an ldap login request to the application.
     *
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function postLdapLogin(Request $request)
    {

        // Check this auth method is allowed
        if (in_array('ldap', $this->authMethods)) {

            // check if there were any errors while connecting
            if (Ldap::hasErrors()) {
                return view('auth.login')
                    ->withErrors(Ldap::errors());
            }

            // Attempt to find the ldap user
            $ldapUser = Ldap::retrieveByCredentials($request->only('username', 'password'));

            // If we found and verified the user, login
            if ($ldapUser) {

                // Check user exists and is active
                $user = $this->userRepository->findOrCreateLdapUser($ldapUser);

                if ($user) {

                    // Login user
                    Auth::login($user, true);
                    return $this->handleUserWasAuthenticated($request, null);
//                    // Redirect to home or admin page, depending on server role
//                    if ($user->role == 'basic') {
//                        return view('app');
//                    } else {
//                        return redirect($this->adminRedirectPath);
//                    }
                }

            }

            // check for any further errors
            if (Ldap::hasErrors()) {
                return view('auth.login')
                    ->withErrors(Ldap::errors());
            }

            // could not authenticate
            return view('auth.login')->withErrors(['ec5_33']);

        }
        // Auth method not allowed
        return view('auth.login')->withErrors(['ec5_55']);

    }

//    /**
//     * Get a validator for an incoming registration request.
//     *
//     * @param  array $data
//     * @return \Illuminate\Contracts\Validation\Validator
//     */
//    protected function validator(array $data)
//    {
//        return Validator::make($data, [
//            'name' => 'required|max:255',
//            'email' => 'required|email|max:255|unique:users',
//            'password' => 'required|min:6|confirmed',
//        ]);
//    }

//    /**
//     * Create a new user instance after a valid registration.
//     *
//     * @param  array $data
//     * @return User
//     */
//    protected function create(array $data)
//    {
//        return User::create([
//            'name' => $data['name'],
//            'email' => $data['email'],
//            'password' => bcrypt($data['password']),
//        ]);
//    }
}
