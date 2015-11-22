<?php

namespace Game\Server;

use Illuminate\Support\Facades\Log;

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
    
    protected $match;
    
    protected $isMapped = false;

    protected static $identifierRegex = '/\[(.*?):(.*?)=(.*?)\]/';


    public function __construct(SessionData $session, $jsonData) {
        
        $message = json_decode($jsonData);
        
        $this->name = $message->type;
        $this->user = $session->getUser()->fresh();
        $this->match = $session->getMatch()->fresh();
        $this->data = ( isset($message->data) ? $message->data : new \stdClass());
        
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
    
    
    public function __set($name, $value) {
        $this->data->$name = $value;
    }
    
    
    public function __get($name) {
        $this->map($name);
        if($this->__isset($name)){
            return $this->data->$name;
        }
    }
    
    
    public function __isset($name) {
        return isset($this->data->$name);
    }
    
    
    public function __unset($name) {
        unset($this->data->$name);
    }
    
    
    protected function map($name){
        if(isset($this->data->$name)){
            if(is_object($this->data->$name)){
                foreach ($this->data->$name as $key => $value) {
                    $this->data->$name->$key = $this->getMatchObject($value);
                }
            } else {
                $this->data->$name = $this->getMatchObject($this->data->$name);
            }
        }
    }
    
    
    protected function mapAll(){
        if($this->isMapped){
            return;
        }
        
        foreach ($this->data as $key => $value){
            $this->data->$key = $this->getMatchObject($value);
        }
        $this->isMapped = true;
    }

    
    protected function getMatchObject($identifierString){
        if(is_string($identifierString)){
            $identifier = $this->getIdentifier($identifierString);
            if($identifier !== null){
                return $this->getObjectFromMatch($identifier->name, $identifier->property, $identifier->value);
            }
        }
        return $identifierString;
    }
    
    
    protected function getObjectFromMatch($fieldName, $fieldPropertyName, $fieldPropertyValue) {
        
        if($this->match->$fieldName instanceof \Illuminate\Database\Eloquent\Collection){
            $matchingElements = $this->match->$fieldName->where($fieldPropertyName, $fieldPropertyValue, false);
            return $matchingElements->first();
        }
        return null;
        
    }
    
    
    protected function getIdentifier($string) {
        if(!is_string($string)){
            return;
        }
        $matchingFields = array();
        $isIdentifier = preg_match(self::$identifierRegex, $string, $matchingFields);
        if($isIdentifier){
            $identifier = new \stdClass();
            $identifier->name = $matchingFields[1];
            $identifier->property = $matchingFields[2];
            $identifier->value = $matchingFields[3];
            return $identifier;
        }
        return;
    }
    
}