<?php namespace ec5\Libraries\Jwt;

use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Firebase\JWT\JWT as FirebaseJwt;
use Exception;
use Config;

class Jwt
{
    /*
    |--------------------------------------------------------------------------
    | Jwt class
    |--------------------------------------------------------------------------
    |
    | This class handles the generating and verifying of JWT tokens
    |
    */
    /**
     * @var array
     */
    private $errors = [];

    /**
     * Jwt constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * Return the errors array.
     *
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * Check if any errors.
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return count($this->errors) > 0 ? true : false;
    }

    /**
     * Generate a JWT token.
     *
     * @param $apiToken
     * @return array JWT Token
     */
    public function generateToken($apiToken)
    {

        // get auth jwt config settings
        $jwtConfig = Config::get('auth.jwt');

        try {
            // Extract the key, from the config file.
            // Note: Can be generated with base64_encode(openssl_random_pseudo_bytes(64));
            $secretKey = $jwtConfig['secret_key'];

            $expiryTime = time() + $jwtConfig['expire'];

            $data = array(
                'iat' => time(), // issued at time
                'jti' => $apiToken, // token id
                'iss' => Config::get('app.url'), // issuer
                'exp' => $expiryTime, // expiry time
                'data' => [ // data

                ]
            );

            // Encode the array to a JWT string.
            $token = FirebaseJwt::encode(
                $data,      // Data to be encoded in the JWT
                $secretKey // The signing key
            );

            return $token;

        } catch (Exception $e) {
            $this->errors = ['ec5_50'];
        }

    }

    /**
     * Verify a JWT token.
     *
     * @param $token
     * @param $returnClaim
     * @return mixed
     */
    public function verifyToken($token, $returnClaim = false)
    {
        /**
         * IMPORTANT:
         * You must specify supported algorithms for your application. See
         * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
         * for a list of spec-compliant algorithms.
         */

        // Get auth jwt config settings
        $jwtConfig = Config::get('auth.jwt');

        $secretKey = $jwtConfig['secret_key'];

        // Attempt to decode the jwt token
        try {

            $decodedToken = FirebaseJwt::decode($token, $secretKey, ['HS256']);

            // token verified
            if ($returnClaim) {
                return $decodedToken;
            }
            return true;

        } catch (Exception $e) {

            // Token invalid:
            // Signature not valid, jwt token expired or altered
            $this->errors = ['ec5_51'];
            return false;
        }

    }

    /**
     * Generate a unique id to store against a user.
     *
     * @param UserContract $user
     * @return string
     */
    public function generateApiToken(UserContract $user)
    {
        // Generate unique id
        $apiToken = uniqid($user->id . '-');

        return $apiToken;

    }

}
