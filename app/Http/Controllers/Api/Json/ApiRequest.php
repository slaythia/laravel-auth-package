<?php

namespace ec5\Http\Controllers\Api\Json;

use Illuminate\Http\Response as BaseResponse;
use Request;

class ApiRequest
{

     /**
     * Contains the url of the request
     *
     * @var string
     */
    public $url;

    /**
     * JWT token
     *
     * @var
     */
    protected $jwt;

    /**
     * The type of the resource ie project, entry
     *
     * @var
     */
    protected $type;

    /**
     * Contains the HTTP method of the request
     *
     * @var string
     */
    public $method;

    /**
     * Contains an optional model ID from the request
     *
     * @var int
     */
    public $id;

    /**
     * Contains any content in request
     *
     * @var string
     */
    public $meta;


    /**
     * The data payload
     *
     * @var
     */
    public $data = [];


    /**
     * Contains an array of linked resource collections to load
     *
     * @var array
     */
    public $included;

    /**
     * Contains an array of column names to sort on
     *
     * @var array
     */
    public $sort;

    /**
     * Contains an array of key/value pairs to filter on
     *
     * @var array
     */
    public $filter;

    /**
     * Specifies the page number to return results for
     * @var integer
     */
    public $pageNumber;

    /**
     * Pagination
     *
     * @var integer
     */
    public $page;

    /**
     * Specifies the number of results to return per page. Only used if
     * pagination is requested (ie. pageNumber is not null)
     *
     * @var integer
     */
    public $pageSize = 50;

    /**
     * Errors array
     *
     * @var array
     */
    protected $errors = array();

    /**
     * The 'type' data ie [data][project], []
     *
     * @var array
     */
    protected $typeData = array();

    /**
     * Relationships
     *
     * @var
     */
    public $relationships;

    /**
     * Attributes
     *
     * @var
     */
    public $attributes;

    /**
     * Constructor.
     *
     * 
     */
    public function __construct()
    {

        $this->url = Request::url();
        $this->method = Request::method();
        if(Request::input('meta')) $this->meta = Request::input('meta');
        if(Request::input('data')) $this->data = Request::input('data');

        // If we have a multipart request, the JSON will not be an object, but a string
        if (preg_match('/multipart\/form\-data/', Request::header('Content-Type'))) {
            // Parse JSON string data to array
            $this->data = json_decode($this->data, true);
            // If JSON not correctly formatted
            if (!$this->data) {
                $this->errors['json-data-tag'] = ['ec5_14'];
                return;
            }
        }

        $this->included = ($i = Request::input('included')) ? explode(',', $i) : $i;
        $this->sort = ($i = Request::input('sort')) ? explode(',', $i) : $i;
        $this->filter = ($i = Request::except('data','meta','sort', 'included', 'page')) ? $i : [];

        $this->page = Request::input('page');
        $this->pageSize = null;
        $this->pageNumber = null;

        $this->_parseRequestContent();

    }

    /**
     * Determine if there were any errors
     *
     * @return bool
     */
    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    /**
     * Return all errors
     *
     * @return array
     */
    public function errors(){
        return $this->errors;
    }

    /**
     * Return the 'data' array
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Return the 'type' 'data' array
     * @return array
     */
    public function getTypeData(){
        return ($this->type != null && isset($this->data[$this->type])) ? $this->data[$this->type] : [] ;
    }

    /**
     * Return any relationships
     *
     * @return mixed
     */
    public function getRelationships()
    {
        return $this->relationships;
    }

    /**
     * Return any attributes
     *
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param $type
     * @return bool
     */
    public function hasType($type)
    {
        return $this->type == $type;
    }

    /**
     * Return the 'content' array
     *
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return JWT token
     *
     * @return mixed
     */
    public function getJwt()
    {
        return $this->jwt;
    }

    /**
     * Parses json_decode content from request by type ie data[$type] into an array of values.
     * set $this->api_data_by_type
     */
    protected function _parseRequestContent()
    {

        if (count($this->data) == 0) {
            $this->errors['json-data-tag'] = ['ec5_14'];
            return;
                
        }

        // set the type attribute
        $this->type = (!empty($this->data['type'])?$this->data['type']:null);

        if ($this->type == null || empty($this->data[$this->type])) {
            $this->errors['json-data-tag'] = ['ec5_14'];
            return;
        }

        // set the remaining class attributes
        $this->jwt = (!empty($this->data['jwt'])?$this->data['jwt']:null);

        $this->attributes = (!empty($this->data['attributes'])?$this->data['attributes']:[]);
        //$this->typeData = $this->data[$this->type];

        $relationships = (!empty($this->data['relationships'])?$this->data['relationships']:[]);
        // parse through relationships, pulling out 'data'
        foreach ($relationships as $key => $value) {

            $data = (!empty($value['data'])?$value['data']:[]);
            $this->relationships[$key] = $data;

        }
       
    }


}
