<?php namespace Game\Services;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

use Game\Exceptions\GameException;

class PolicyComplianceService {
    
    
        protected $policies = [
            
            
            /*
             * Policies for registering and changing users
             */
                "new_user_name" => "unique:users,name|required|min:5|max:32|alpha_dash",
                "new_user_email" => "unique:users,email|required|email",
                "new_user_password" => "required|min:6|max:20_alpha_dash|confirmed",
                "new_user_password_confirmation" => "",
                "new_user_avatarfile" => "",
                "password_reset_token" => "required",
            
            /*
             * Policies for login
             */
                "user_email" => "required|email",
                "user_password" => "required",
            
            /*
             * Policies for the messaging component
             */
                "thread_subject" => "required|min:6|max:32",
                "thread_recipients" => "selected",
                "thread_message_text" => "required",
                "thread_reuseexistingthread" => "",
            
            /*
             * Policies for the match component
             */
                "match_name" => "required|min:6|max:32",
                "match_map_name" => "required",
                "match_maximum_users" => "required|integer|between:2,6",
                "match_invited_users" => "selected",
                "match_user_colorscheme" => "required",
            
        ];


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
                    
                    $attributeNames[$name] = Lang::get("input." . $name);

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
