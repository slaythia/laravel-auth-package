<?php namespace ec5\Repositories\Eloquent\User;

use ec5\Libraries\Ldap\LdapUser;
use ec5\Models\Users\User;
use Exception;

trait CreateRepository {

    /**
     * @param $input
     * @return static
     */
    public function create($input)
    {
        return $this->tryUserCreate($input);
    }

    private function tryUserCreate($input)
    {
        try {

            return User::create($input);

        } catch (Exception $e) {

            $this->errors = ['ec5_39'];
        }

    }

    /**
     * Return social user if exists; create and return if doesn't
     *
     * @param $provider
     * @param $providerUser
     * @return User
     */
    public function findOrCreateSocialUser($provider, $providerUser)
    {
        // Check if we already have registered
        $user = $this->where('email', '=', $providerUser->email);

        if (!$user) {
            // If not, create new
            $user =  $this->create([
                'name' => $providerUser->name,
                'email' => $providerUser->email,
                'provider' => $provider,
                'state' => 'active',
                'server_role' => 'basic'
            ]);
        }

        // Check user is active
        if ($user->state == 'active') {
            // update user avatar
            $user->avatar = $providerUser->avatar;
            $user->update();

            return $user;
        }

    }

    /**
     * Return user if exists; create and return if doesn't
     *
     * @param LdapUser $ldapUser
     * @return User
     */
    public function findOrCreateLdapUser(LdapUser $ldapUser)
    {
        // Check if we already have registered
        $user = $this->where('email', '=', $ldapUser->getAuthIdentifier());

        if (!$user) {
            // If not, create new
            $user = $this->create([
                'name' => $ldapUser->getName(),
                'email' => $ldapUser->getAuthIdentifier(),
                'provider' => 'ldap',
                'state' => 'active',
                'role' => 'basic'
            ]);
        }

        // Check user is active
        if ($user->state == 'active') {
            return $user;
        }

    }

}