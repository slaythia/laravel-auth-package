<?php namespace ec5\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller {

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
