<?php

namespace Game\Server;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

/**
 * Description of SessionData
 *
 * @author birdwellow
 */
class ServerEvent {
    
    protected $name;
    protected $data;
    protected $match;
    protected $forUsers;
    
    public static $FOR_ALL = "for:all";
    public static $FOR_OTHERS = "for:others";
    public static $FOR_SELF = "for:self";


    public function __construct($name, $data, $match, $forUsers = "for:all") {
        $this->name = $name;
        $this->data = $data;
        $this->match = $match;
        $this->forUsers = $forUsers;
    }


    public function getName(){
        return $this->name;
    }


    public function getData(){
        return $this->data;
    }


    public function getMatch(){
        return $this->match;
    }


    public function getForUsers(){
        return $this->forUsers;
    }


    public function isForAll(){
        return ( $this->forUsers == self::$FOR_ALL );
    }


    public function isForAllOthers(){
        return ( $this->forUsers == self::$FOR_OTHERS );
    }


    public function isForSender(){
        return ( $this->forUsers == self::$FOR_SELF );
    }
    
    
    public function toJson() {
        
        $this->encodeData();
        return json_encode([
            "type" => $this->name,
            "data" => $this->data
        ]);
    }
    
    protected function encodeData(){
        foreach($this->data as $key => $value){
            if($value instanceof Model){
                $this->data->$key = $value->socketIdentifier();
            }
        }
    }
}
