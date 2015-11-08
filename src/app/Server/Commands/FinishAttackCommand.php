<?php

namespace Game\Server\Commands;

use Cmgmyr\Messenger\Models\Message;

use Illuminate\Support\Facades\Log;

use Game\Server\SocketEvent;
use Game\Server\ServerEvent;
use Game\Model\Match;

/**
 * Description of PerformAttackCommand
 *
 * @author birdwellow
 */
class FinishAttackCommand extends AbstractGameFlowControllerCommand {
    
    public function perform(SocketEvent $event, Match $match){
        
        return new ServerEvent("phase.troopshift", $event->getData(), $match);
        
    }
    
}