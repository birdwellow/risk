<?php

namespace Game\Server;

use Game\Server\SessionData;

/**
 * Description of SessionData
 *
 * @author birdwellow
 */
class SocketEvent {
    
    protected $name;
    protected $user;
    protected $data;


    public function __construct(SessionData $session, $jsonData) {
        
        $message = json_decode($jsonData);
        
        $this->name = $message->type;
        $this->user = $session->getUser();
        $this->data = ( isset($message->data) ? $message->data : array());
        
    }


    public function getName(){
        return $this->name;
    }


    public function getUser(){
        return $this->user;
    }


    public function getData(){
        return $this->data;
    }


    public function getDataAttribute($key){
        if(isset($this->data)){
            if(isset($this->data->$key)){
                return $this->data;
            }
            if(is_array($this->data) && isset($this->data[$key])){
                return $this->data;
            }
        }
        return null;
    }
    
}
