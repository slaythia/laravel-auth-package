<?php

namespace ec5\Http\Controllers\Api\Json;

use Illuminate\Http\JsonResponse;

use Lang;
/**
 *
 */
class ApiResponse
{
    /**
     * An array of parameters.
     *
     * @var array
     */
    protected $responseData = [];

    /**
     * The main response.
     *
     * @var array|object
     */
    protected $body;

    /**
     * HTTP status code
     *
     * @var int
     */
    protected $httpStatusCode;

    /**
     * Constructor
     *
     * @param array|object $body
     */
    public function __construct()
    {
        //$this->httpStatusCode = $httpStatusCode;
    }

    /**
     * here we set keys ie relationships, links etc BUT IF  == 'body' 
     * the type like project or entries will be set to $this->body var
     * 
     * @param string $key
     * @param mixed  $value
     */
    public function __set($key, $value)
    {
        if ($key == 'body') {
            $this->body = $value;
            return;
        }
        $this->responseData[$key] = $value;
    }

    /**
     * Returns the JsonResponse.
     * Array of all values used by __set and the main Body value
     *
     * @param $httpStatusCode
     * @param string $bodyKey
     * @param int $options
     * @param array $additionalHeaders
     * @return JsonResponse
     */
    public function toJsonResponse($httpStatusCode, $bodyKey = 'data', $options = 0, $additionalHeaders = [])
    {
        return new JsonResponse(array_merge(
            [ $bodyKey => $this->body ],
            $this->responseData
        ), $httpStatusCode, array_merge(['Content-Type' => 'application/vnd.api+json'], $additionalHeaders), $options);
    }

    /**
     * Returns the Json error response
     *
     * @param $httpStatusCode
     * @param array $errors
     * @param array $extra
     * @return JsonResponse
     */
    public function errorResponse($httpStatusCode, array $errors, array $extra=array())
    {

        // out array
        $outArray = [];

        // loop though $errors and format into api error array
        foreach ($errors as $key => $value) {
            // temp array to store error, expecting array otherwise skip
            if(!is_array($value)) continue;
            foreach ($value as $key2 => $value2) {
                $tempArray = [];
                $tempArray['code'] = $value2;
                $tempArray['title'] = Lang::get('status_codes.' . $value2);
                $tempArray['source'] = $key;
                // add temp error array to out array
                $outArray[] = $tempArray;
            }
            
        }

        return new JsonResponse(array_merge(
            [ 'errors' => $outArray ],$extra)
        , $httpStatusCode, ['Content-Type' => 'application/vnd.api+json'], $options=0);

    }
}
