<?php

namespace ec5\Http\Validation;

use Validator;
use Lang;

class Handler
{
    
    protected $rules = array(); //overwrite in child

    protected $errors = array();

    protected $messages = [
        'required' => 'ec5_21',
        'confirmed' => 'ec5_40',
        'unique' => 'ec5_41',
        'min' => 'ec5_43',
        'max' => 'ec5_44',
        'email' => 'ec5_42',
        'in' => 'ec5_29',
        'token' => 'ec5_21',
        'array' => 'ec5_29',
        'date_format' => 'ec5_79',
        'mimes' => 'ec5_81'
    ];

    protected $data = array();
    
 
    /**
     * return the errors array
     *
     * @return array 
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * return count of errors array
     *
     * @return boolean 
     */
    public function hasErrors(){
        return count($this->errors) > 0;
    }

    /**
     * Apply rules to data array in.
     * if we want to make sure that the keys in match the rules then check_keys == true
     * @param  array $data
     * @param  check_keys 
     * @return boolean or additionChecks() returns boolean
     */

    public function validate($data, $check_keys = false)
    {
        try {

            //reset
            $this->errors = array();
            $this->data = $data;

            // to make sure only the keys we defined
            if($check_keys) {

                $missing_keys = array_diff_key($this->rules,$data);
                $extra_keys = array_diff_key($data,$this->rules);
                $keyError = $this->checkKeys($missing_keys,$extra_keys);
                if($keyError) return false;

            }

            //TODO move to genral place ?
            Validator::extendImplicit('alpha_num_under_spaces', function($attribute, $value, $parameters)
            {
                return preg_match('/(^[A-Za-z0-9_ ]+$)+/', $value);
            });

             // make a new validator object
            $v = Validator::make($data, $this->rules, $this->messages);

            // check for failure
            if ($v->fails())
            {
                $this->errors = array_merge($this->errors, $v->errors()->getMessages());
                return false;
            }

            // validation pass
            return true;

        } catch (Exception $e) {
            
            //add error ?
            return false;
        }

        return false;
        
    }//end class

    /**
     * check if keys are extra/missing compare rules and data in
     *
     * @param  array $data
     * @param  array $data
     * @return boolean 
     */
    protected function checkKeys($missing_keys,$extra_keys){

        $hasError = false;
        $errors = array();

        if( count( $missing_keys ) > 0 ){
            $hasError = true;
            $this->errors['missing_keys'] = ['ec5_60'];
            foreach (array_keys($missing_keys)  as $key => $value) {
                $this->errors[$key] = [$value];
                # code...
            }
        }

        if( count( $extra_keys ) > 0 ){
            $hasError = true;
            $this->errors['extra_keys'] = ['ec5_61'];
            foreach (array_keys($extra_keys)  as $key => $value) {
                $this->errors[$key] = [$value];
                # code...
            }
        }
        

        return $hasError;
    }


    protected function addAdditionalError($inputRef,$code){
         $this->errors[$inputRef] = [$code];
    }

    protected function isValidRef($ref){

        $inputRef = (isset($this->data['ref'])) ? $this->data['ref'] : '';

        if (preg_match("/^{$ref}+_[a-zA-Z0-9]{13}$/", $inputRef) ) {
             //$this->errors[$inputRef] = ["has Ref {$ref} -- {$inputRef}"];
        }else{
            $this->errors[$inputRef] = ["no match has Ref {$ref} -- {$inputRef}"];
        }
    }

    
}// end class
