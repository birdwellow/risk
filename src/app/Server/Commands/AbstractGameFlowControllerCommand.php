<?php

namespace Game\Server\Commands;

use Game\Server\SocketEvent;
use Game\Model\Match;

/**
 * Description of AbstractGameFlowControllerCommand
 *
 * @author birdwellow
 */
abstract class AbstractGameFlowControllerCommand {

    public function __construct() {
        
    }
    
    abstract public function perform(SocketEvent $event, Match $match);
    
}
