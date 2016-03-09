<?php namespace ec5\Http\Controllers;

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
