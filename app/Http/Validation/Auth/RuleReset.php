<?php

namespace ec5\Http\Validation\Auth;

use ec5\Http\Validation\Handler;

class RuleReset extends Handler
{
    protected $rules = [
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|confirmed|min:6',
    ];

    /**
     * Additional checks
     */
    public function additionalChecks()
    {
        //
    }
}
