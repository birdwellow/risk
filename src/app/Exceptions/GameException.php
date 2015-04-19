<?php namespace Game\Exceptions;

use Exception;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GameException
 *
 * @author fvo
 */
class GameException extends Exception {
    
    
    protected $errorKey;
    
    protected $customData;


    protected static $errorData = [
        
        "USER.ALREADY.JOINED" => [
            "message" => "error.user.already.joined",
            "category" => "error.user"
        ],
        "USER.ALREADY.JOINED.ANOTHER.MATCH" => [
            "message" => "error.user.already.joined.another.match",
            "category" => "error.user"
        ],
        "USER.CANNOT.DELETE.MATCH" => [
            "message" => "error.user.cannot.delete.match",
            "category" => "error.user"
        ],
        "USER.INVALID.OPTIONS" => [
            "message" => "error.user.invalid.options",
            "category" => "error.user"
        ],
        
        
        "MATCH.NOT.FOUND" => [
            "message" => "error.system.match.not.found",
            "category" => "error.system"
        ],
        
    ];
    
    
    public function __construct($errorKey, $customData = null) {
        
        parent::__construct("Logical Game Exception occurred", 0, null);
        $this->errorKey = $errorKey;
        $this->customData = $customData;
        
    }
    
    
    public function getUIMessageKey() {
        if(isset(self::$errorData[$this->errorKey]["message"])){
            return self::$errorData[$this->errorKey]["message"];
        }
        if(isset(self::$errorData[$this->errorKey][0])){
            return self::$errorData[$this->errorKey][0];
        }
    }
    
    
    public function getCategory() {
        if(isset(self::$errorData[$this->errorKey]["category"])){
            return self::$errorData[$this->errorKey]["category"];
        }
        if(isset(self::$errorData[$this->errorKey][1])){
            return self::$errorData[$this->errorKey][1];
        }
    }
    
    
    public function getMessageKey() {
        if(isset(self::$errorData[$this->errorKey]["severity"])){
            return self::$errorData[$this->errorKey]["severity"];
        }
        if(isset(self::$errorData[$this->errorKey][3])){
            return self::$errorData[$this->errorKey][3];
        }
    }
    
    
    public function getCustomData() {
        return $this->customData;
    }
    
}
