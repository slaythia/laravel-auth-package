<?php

namespace ec5\Libraries\Jwt;

use ec5\Models\Contracts\ApiAuthorizableContract as ApiUserContract;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Contracts\Auth\UserProvider;
use ec5\Models\Contracts\ApiUserProvider;
use ec5\Libraries\Ldap\LdapUser;
use Illuminate\Support\Str;
use ec5\Models\Users\User;

class JwtUserProvider implements UserProvider, ApiUserProvider
{

    /**
     * The hasher implementation.
     *
     * @var \Illuminate\Contracts\Hashing\Hasher
     */
    protected $hasher;

    /**
     * The Eloquent user model.
     *
     * @var string
     */
    protected $model;

    /**
     * Create a new database user provider.
     *
     * @param HasherContract $hasher
     * @param User $model
     */
    public function __construct(HasherContract $hasher, User $model)
    {
        $this->model = $model;
        $this->hasher = $hasher;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return $this->createModel()->newQuery()->find($identifier);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed $identifier
     * @param  string $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        $model = $this->createModel();

        return $model->newQuery()
            ->where($model->getAuthIdentifierName(), $identifier)
            ->where($model->getRememberTokenName(), $token)
            ->first();
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  string $token
     * @return void
     */
    public function updateRememberToken(UserContract $user, $token)
    {
        $user->setRememberToken($token);

        $user->save();
    }

    /**
     * Update the api token for the given user in storage.
     *
     * @param  ApiUserContract $user
     * @param  string $token
     * @return void
     */
    public function updateApiToken(UserContract $user, $token)
    {
        $user->updateApiToken($token);

        $user->save();
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // Eloquent User "model" that will be utilized by the Guard instances.
        $query = $this->createModel()->newQuery();

        foreach ($credentials as $key => $value) {
            if (!Str::contains($key, 'password')) {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(UserContract $user, array $credentials)
    {
        $plain = $credentials['password'];

        return $this->hasher->check($plain, $user->getAuthPassword());
    }

    /**
     * Create a new instance of the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createModel()
    {
        $class = '\\' . ltrim($this->model, '\\');

        return new $this->model;
    }

    /**
     * Gets the hasher implementation.
     *
     * @return \Illuminate\Contracts\Hashing\Hasher
     */
    public function getHasher()
    {
        return $this->hasher;
    }

    /**
     * Sets the hasher implementation.
     *
     * @param  \Illuminate\Contracts\Hashing\Hasher $hasher
     * @return $this
     */
    public function setHasher(HasherContract $hasher)
    {
        $this->hasher = $hasher;

        return $this;
    }

    /**
     * Gets the name of the Eloquent user model.
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Sets the name of the Eloquent user model.
     *
     * @param  string $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Return social user if exists; create and return if doesn't
     *
     * @param $provider
     * @param $providerUser
     * @return UserContract|null
     */
    public function findOrCreateSocialUser($provider, $providerUser)
    {
        // Check if we already have registered
        $user = $this->model->where('email', '=', $providerUser->email)->first();

        if (!$user) {
            // If not, create new
            $user = $this->retrieveByCredentials([
                'name' => $providerUser->name,
                'email' => $providerUser->email,
                'open_id' => $providerUser->id,
                'provider' => $provider,
                'state' => 'active',
                'server_role' => 'basic'
            ]);
        }

        // check user is active
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
     * @return UserContract|null
     */
    public function findOrCreateLdapUser(LdapUser $ldapUser)
    {
        // Check if we already have registered
        $user = $this->model->where('email', '=', $ldapUser->getAuthIdentifier())->first();

        if (!$user) {
            // If not, create new
            $user = $this->retrieveByCredentials([
                'name' => $ldapUser->getName(),
                'email' => $ldapUser->getAuthIdentifier(),
                'provider' => 'ldap',
                'state' => 'active',
                'role' => 'basic'
            ]);
        }

        // check user is active
        if ($user->state == 'active') {
            return $user;
        }

    }

}