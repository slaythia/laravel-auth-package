<?php

namespace ec5\Http\Controllers\Admin;

use Illuminate\Http\Request;
use ec5\Http\Requests;
use ec5\Repositories\Eloquent\User\UserRepository;
use ec5\Http\Controllers\Controller;

class AdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the server administration tasks
    |
    */

    /**
     * @var UserRepository $userRepository
     */
    protected $userRepository;

    /**
     * Create a new admin controller instance.
     * Restricted to admin and superadmin users
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        // Set middleware to admin, which covers super admin/admin server roles
        $this->middleware('auth.admin');
        $this->userRepository = $userRepository;
    }

    /**
     * Display a list of users, paginated, against an optional search/filter query
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Get request data
        $data = $request->all();
        $perPage = 10;

        // Set search/filter/filter option defaults
        $search = !empty($data['search']) ? $data['search'] : '';
        $options['filter'] = !empty($data['filter']) ? $data['filter'] : '';
        $options['filter_option'] = !empty($data['filterOption']) ? $data['filterOption'] : '';
        $currentPage = !empty($data['page']) ? $data['page'] : 1;

        $users = $this->userRepository->paginate($perPage, $currentPage, $search, $options);

        // If ajax, return rendered html
        if ($request->ajax()) {
            return response()->json(view('admin.users', ['users' => $users])->render());
        }

        // Return view with relevant params
        return view('admin.admin', ['users' => $users]);

    }

}