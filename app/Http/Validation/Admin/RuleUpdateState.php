<?php

namespace ec5\Http\Validation\Admin;

use ec5\Http\Validation\Handler;

class RuleUpdateState extends Handler
{
    protected $rules = [
        'state' => 'required|in:active,disabled'
    ];

    /**
     * Additional checks
     */
    public function additionalChecks()
    {
        //
    }
}
