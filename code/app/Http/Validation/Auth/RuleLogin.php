<?php

namespace ec5\Http\Validation\Auth;

use ec5\Http\Validation\Handler;

class RuleLogin extends Handler
{
    protected $rules = [
        'email' => 'required',
        'password' => 'required'
    ];

    /**
     * Additional checks
     */
    public function additionalChecks()
    {
        //
    }
}
