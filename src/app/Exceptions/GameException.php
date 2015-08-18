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
        
        "NOT.PARTICIPANT.OF.THREAD" => [
            "message" => "error.not.participant.of.thread",
            "category" => "error.user"
        ],
        "ALREADY.JOINED" => [
            "message" => "error.already.joined",
            "category" => "error.user"
        ],
        "ALREADY.JOINED.ANOTHER.MATCH" => [
            "message" => "error.already.joined.another.match",
            "category" => "error.user"
        ],
        "CANNOT.DELETE.MATCH" => [
            "message" => "error.cannot.delete.match",
            "category" => "error.user"
        ],
        "CANNOT.ADMINISTRATE.MATCH" => [
            "message" => "error.cannot.administrate.match",
            "category" => "error.user"
        ],
        "ONLY.INVITED.USERS" => [
            "message" => "error.only.invited.users",
            "category" => "error.user"
        ],
        "INVALID.OPTIONS" => [
            "message" => "error.options.notsaved",
            "category" => "error.user"
        ],
        "CREATE.MATCH.WRONG.PARAMETERS" => [
            "message" => "error.create.match.wrong.parameters",
            "category" => "error.user"
        ],
        "JOIN.MATCH.WRONG.PARAMETERS" => [
            "message" => "error.join.match.wrong.parameters",
            "category" => "error.user"
        ],
        "ADMINISTRATE.MATCH.WRONG.PARAMETERS" => [
            "message" => "error.administrate.match.wrong.parameters",
            "category" => "error.user"
        ],
        "PASSWORD.NOT.CHANGED" => [
            "message" => "error.password.notchanged",
            "category" => "error.user"
        ],
        "THREAD.NOT.CREATED" => [
            "message" => "error.thread.notcreated",
            "category" => "error.user"
        ],
        "MESSAGE.NOT.SENT" => [
            "message" => "error.message.notsent",
            "category" => "error.user"
        ],
        "USERS.NOT.ADDED.TO.THREAD" => [
            "message" => "error.thread.nousersadded",
            "category" => "error.user"
        ],
        "LOGIN.ERROR" => [
            "message" => "error.login.credentials.invalid",
            "category" => "error.user"
        ],
        "REGISTRATION.ERROR" => [
            "message" => "error.registration.data.invalid",
            "category" => "error.user"
        ],
        "PASSWORDRESET.EMAIL.NOT.SENT" => [
            "message" => "error.passwordreset.email.not.sent",
            "category" => "error.user"
        ],
        
        
        "MATCH.NOT.FOUND" => [
            "message" => "error.match.not.found",
            "category" => "error.system"
        ],
        "THREAD.NOT.FOUND" => [
            "message" => "error.thread.not.found",
            "category" => "error.system"
        ],
        "USER.NOT.FOUND.BY.EMAIL" => [
            "message" => "error.user.not.found.by.email",
            "category" => "error.system"
        ],
        
        "MATCH.CLOSED" => [
            "message" => "error.match.closed",
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
