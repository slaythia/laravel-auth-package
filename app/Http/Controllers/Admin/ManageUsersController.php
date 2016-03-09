<?php

namespace ec5\Http\Controllers\Admin;

use Illuminate\Http\Request;
use ec5\Http\Requests;
use Redirect;
use ec5\Repositories\Eloquent\User\UserRepository;
use ec5\Http\Controllers\Controller;
use ec5\Http\Validation\Admin\RuleAddUser as AddUserValidator;
use ec5\Http\Validation\Admin\RuleUpdateState as UpdateStateValidator;
use ec5\Http\Validation\Admin\RuleUpdateServerRole as UpdateServerRoleValidator;
use ec5\Http\Controllers\Api\Json\ApiResponse;
use Lang;

class ManageUsersController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Manage Users Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the management of users from a server administrator
    |
    */

    /**
     * @var UserRepository object
     */
    protected $userRepository;

    /**
     * Create a new manager users controller instance.
     * Restricted to admin and superadmin users
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->middleware('auth.admin');
        $this->userRepository = $userRepository;

    }

    /**
     * Handle a request to update a user role
     *
     * @param  \Illuminate\Http\Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */

    /**
     * @param Request $request
     * @param ApiResponse $apiResponse
     * @param UpdateServerRoleValidator $validator
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUserServerRole(Request $request, ApiResponse $apiResponse, UpdateServerRoleValidator $validator)
    {
        // Get request data
        $input = $request->all();

        // Validate the data
        $validator->validate($input);
        if ($validator->hasErrors()) {

            if ($request->ajax()) {
                return $apiResponse->errorResponse(499, $validator->errors());
            }
            return redirect()->back()->withErrors($validator->errors());
        }

        // Attempt to update the user with supplied field and value
        if ($this->userRepository->updateUserByAdmin($input['email'], 'server_role', $input['server_role'])) {

            // If ajax, return success 200 code
            if ($request->ajax()) {
                return $apiResponse->toJsonResponse(200);
            }
            // Redirect back to admin page
            return redirect()->back();
        }

        // retrieve error message
        if ($this->userRepository->hasErrors()) {
            $errors = $this->userRepository->errors();
        } else {
            $errors = ['ec5_49'];
        }

        if ($request->ajax()) {
            return $apiResponse->errorResponse(499, ['update-user-server-role' => $errors]);
        }

        // redirect back to admin page
        return redirect()->back()->withErrors($errors);

    }

    /**
     * Handle a request to update a user state
     *
     * @param  \Illuminate\Http\Request  $request
     * @param ApiResponse $apiResponse
     * @param UpdateStateValidator $validator
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function updateUserState(Request $request, ApiResponse $apiResponse, UpdateStateValidator $validator)
    {
        // Get request data
        $input = $request->all();

        // Validate the data
        $validator->validate($input);
        if ($validator->hasErrors()) {

            if ($request->ajax()) {
                return $apiResponse->errorResponse(499, $validator->errors());
            }
            return redirect()->back()->withErrors($validator->errors());
        }

        // Attempt to update the user with supplied field and value
        if ($this->userRepository->updateUserByAdmin($input['email'], 'state', $input['state'])) {
            // if ajax, return success json
            if ($request->ajax()) {
                return $apiResponse->toJsonResponse(200);
            }
            // Redirect back to admin page
            return redirect()->back();
        }

        // retrieve error message
        if ($this->userRepository->hasErrors()) {
            $errors = $this->userRepository->errors();
        } else {
            $errors = ['ec5_34'];
        }

        if ($request->ajax()) {
            return $apiResponse->errorResponse(499, ['update-user-state' => $errors]);
        }

        return redirect()->back()->withErrors($errors);

    }

    /**
     * Handle a registration request for the application via admin page.
     *
     * @param Request $request
     * @param AddUserValidator $validator
     * @return $this
     */
    public function postRegisterByAdmin(Request $request, AddUserValidator $validator)
    {
        $input = $request->all();

        // Validate the data
        $validator->validate($input);
        if ($validator->hasErrors()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        // Create the new user
        $user = $this->userRepository->create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => bcrypt($input['password']),
            'provider' => 'local',
            'state' => 'active',
            'server_role' => 'basic'
        ]);

        // If successfully created
        if ($user) {
            // Redirect back to admin page
            return redirect()->back()->with('message', 'ec5_35');
        }

        if ($this->userRepository->hasErrors()) {
            $errors = $this->userRepository->errors();
        } else {
            $errors = ['ec5_39'];
        }

        // Redirect back to admin page with errors
        return redirect()->back()->withErrors($errors);
    }

    /**
     * Search for users by email
     *
     * @param Request $request
     * @return array
     */
    public function searchByEmail(Request $request)
    {

        // Get request data
        $input = $request->all();

        $users = $this->userRepository->searchByEmail($input['query']);

        // If ajax, return rendered html
        if ($request->ajax()) {

            return $users;
        }


    }

}