<?php

namespace ec5\Http\Validation\Auth;

use ec5\Http\Validation\Handler;

class RuleForgot extends Handler
{
    protected $rules = [
        'email' => 'required|email'
    ];

    /**
     * Additional checks
     */
    public function additionalChecks()
    {
        //
    }
}
