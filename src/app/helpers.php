<?php

    function invalid( $inputName ) {
        
        $invalidFields = session("invalidfields");
        
        if($invalidFields && in_array($inputName, $invalidFields)){
            return "invalid";
        }
        
    }

    function oldordefault( $inputName, $default = "" ) {
        
        $old = old($inputName);
        if($old){
            return $old;
        } else {
            return $default;
        }
        
    }

?>