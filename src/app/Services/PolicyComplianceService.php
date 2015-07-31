<?php namespace Game\Services;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

use Game\Exceptions\GameException;

class PolicyComplianceService {
    
    
        protected $policies = [
            
                "username" => "unique:users,name|required|min:5|max:32|alpha_dash",
                "email" => "unique:users|required|email",
                "new.password" => "required|min:4|max:20_alpha_dash|confirmed",
                "old.password" => "required",
                "message.text" => "required",
                "thread.subject" => "required|min:6|max:32",
                "thread.recipients" => "selected"
            
        ];


        public function __construct() {

        }


        public function check($attributes, $errorKey = "") {

                Validator::extend("auth", function($attribute, $value, $parameters){
                    
                    return Auth::validate(['email' => $parameters[0], 'password' => $value]);

                });

                Validator::extend("selected", function($attribute, $value, $parameters){
                    
                    return $value > 0;

                });

                $values = array();
                $rules = array();
                $attributeNames = array();

                foreach ($attributes as $name => $attribute) {

                    if(!is_array($attribute)){
                        $attribute = array($attribute);
                    }
                    
                    $values[$name] = $attribute[0];
                    
                    $rule = "";
                    if(isset($this->policies[$name])){
                        $rule .= $this->policies[$name];
                    }
                    if(isset($attribute[1]) && $attribute[1]){
                        if($rule){
                            $rule .= "|".$attribute[1];
                        } else {
                            $rule .= $attribute[1];
                        }
                        
                    }
                    $rules[$name] = $rule;
                    
                    $attributeNames[$name] = Lang::get("message.field." . $name);

                }
                
                $validator = Validator::make($values, $rules);
                $validator->setAttributeNames($attributeNames);

                if($validator->fails()){
                    Session::flash("invalidfields", array_keys($validator->failed()));
                    throw new GameException(
                            $errorKey,
                            $validator->messages(),
                            $attributeNames
                    );
                }

        }
    
}
