<?php namespace Game\Handlers\Messages;

use Game\Handlers\Messages\UIFeedback;

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
class ErrorFeedback extends UIFeedback {
    
    public $type = "error";
    public $messageKey;
    public $hints;
    
    public function __construct($messageKey, $hints = null){
        parent::__construct($messageKey, $hints);
    }
    
    
}
