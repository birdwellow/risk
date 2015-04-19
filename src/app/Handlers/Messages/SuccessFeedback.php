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
class SuccessFeedback extends UIFeedback {
    
    public $type = "success";
    public $messageKey;
    public $hints;
    
    public function __construct($messageKey, $hints = null){
        parent::__construct($messageKey, $hints);
    }
    
    
}
