<?php namespace ec5\Libraries\Ldap;

use Illuminate\Contracts\Auth\Authenticatable as Authenticatable;

class LdapUser implements Authenticatable
{
    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $username
     */
    protected $username;


    /**
     * @var string $open_id
     */
    protected $open_id = '';


    /**
     * @var string $dn
     */
    protected $dn;

    /**
     * @var array $member_of
     */
    protected $member_of = [];

    /**
     * @var array $schools
     */
    protected $schools = [];

    /**
     * The user name attribute
     *
     * @var
     */
    protected $userNameAttribute;

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->username;
    }

    /**
     * Get the name for the user.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        // this shouldn't be needed as you cannot directly access the password
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        // this shouldn't be needed as user / password is in ldap
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string $value
     * @return void
     */
    public function setRememberToken($value)
    {
        // this shouldn't be needed as user / password is in ldap
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        // this shouldn't be needed as user / password is in ldap
    }

    /**
     * Setting the current User
     *
     * @param array $details
     */
    public function setEntry(array $details)
    {
        // set the user name attribute
        $this->userNameAttribute = $details['userNameAttribute'];

        // retrieve the relevant user name attribute value
        $this->username = $details['entry'][$this->userNameAttribute][0];
        // set other fields
        $this->name = (!empty($details['entry']['cn'][0])?$details['entry']['cn'][0]:'');
        $this->dn = $details['entry']['dn'];

    }

    /**
     * Return distinguished name of the User
     *
     * @return string
     */
    public function getDn()
    {
        return $this->dn;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->member_of;
    }

    /**
     * @param string $group
     * @return bool
     */
    public function isMemberOf($group)
    {
        foreach($this->member_of as $groups) {
            if( preg_match('/^CN=' . $group . '/', $groups) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

}