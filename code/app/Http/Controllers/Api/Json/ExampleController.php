<?php

namespace ec5\Http\Controllers\Api\Json;

use ec5\Http\Controllers\Controller;


class ExampleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        return view('auth.jwt-test');

    }

}
