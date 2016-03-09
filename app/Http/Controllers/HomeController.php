<?php namespace ec5\Http\Controllers;

use Auth;

class HomeController extends Controller {

    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show home page (available to all users)
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('app');
    }

}
