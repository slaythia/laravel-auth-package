<?php

namespace ec5\Models\Contracts;

use Illuminate\Contracts\Auth\Authenticatable as UserContract;

/**
 * Custom Interface for enforcing the contract that a user provider must
 * specify an updateApiToken method, for updating an api_token
 *
 * Interface ApiUserProvider
 * @package ec5\Models\Contracts
 */
interface ApiUserProvider
{
    /**
     * Update the api token for the given user in storage.
     *
     * @param  UserContract  $user
     * @param  string  $token
     * @return void
     */
    public function updateApiToken(UserContract $user, $token);

}
