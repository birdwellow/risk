<?php namespace Game\Handlers\Messages;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserFeedback
 *
 * @author fvo
 */
class ErrorFeedback {
    
    public $type = "error";
    public $messageKey;
    public $hints;
    
    public function __construct($messageKey, $hints = null){
        $this->messageKey = $messageKey;
        $this->hints = $hints;
    }
    
    
}
