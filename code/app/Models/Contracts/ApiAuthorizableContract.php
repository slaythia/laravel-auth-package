<?php

namespace ec5\Models\Contracts;

/**
 * Custom Interface for enforcing the contract that a user provider must
 * specify an updateApiToken method, for updating an api_token
 *
 * Interface ApiAuthorizableContract
 * @package ec5\Models\Contracts
 */
interface ApiAuthorizableContract
{
    /**
     * Update the api token for the given user in storage.
     *
     * @param  string  $token
     * @return void
     */
    public function updateApiToken($token);

}
