<?php

namespace ec5\Models\Users;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use ec5\Models\Contracts\ApiAuthorizableContract;

class User extends Model implements AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    ApiAuthorizableContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'avatar', 'open_id', 'provider', 'state', 'server_role'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token', 'open_id'];

    /**
     * Always capitalise each name when we save it to the database.
     * @param $value
     */
    public function setNameAttribute($value) {
        $this->attributes['name'] = ucwords($value);
    }

    /**
     * Always lowercase each email when we save it to the database.
     * @param $value
     */
    public function setEmailAttribute($value) {
        $this->attributes['email'] = strtolower($value);
    }

    /**
     * Determine if the current user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->server_role == 'admin';
    }

    /**
     * Determine if the current user is a super admin.
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->server_role == 'superadmin';
    }

    /**
     * Determine if the current user is a super admin.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->state == 'active';
    }

    /**
     * Determine if the current user has a specified role.
     *
     * @param $role
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->server_role == $role;
    }

    /**
     * Update the api token for the user.
     *
     * @param  string  $token
     * @return void
     */
    public function updateApiToken($token)
    {
        $this->api_token = $token;
    }

}
