<?php

namespace ec5\Http\Validation\Admin;

use ec5\Http\Validation\Handler;

class RuleAddUser extends Handler
{
    protected $rules = [
        'name' => 'required|max:100',
        'email' => 'required|email|max:255|unique:users',
        'password' => 'required|confirmed|min:6|max:255'
    ];

    /**
     * Additional checks
     */
    public function additionalChecks()
    {
        //
    }
}
