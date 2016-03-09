<?php

namespace ec5\Http\Validation\Admin;

use ec5\Http\Validation\Handler;

class RuleUpdateServerRole extends Handler
{
    protected $rules = [
        'server_role' => 'required|in:basic,admin'
    ];

    /**
     * Additional checks
     */
    public function additionalChecks()
    {
        //
    }
}
