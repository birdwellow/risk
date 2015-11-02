<?php

namespace Game\Server\Commands;

use Illuminate\Support\Facades\Log;

use Game\Server\SocketEvent;
use Game\Server\ServerEvent;
use Game\Model\Match;

/**
 * Description of PerformAttackCommand
 *
 * @author birdwellow
 */
class UserDisconnectCommand extends AbstractGameFlowControllerCommand {
    
    public function perform(SocketEvent $event, Match $match){
        
        $user = $event->getUser();
        $user->isonline = false;
        $user->save();
        
        $event->user = $user;
        
        return new ServerEvent("user.disconnected", $event->getData(), $match, ServerEvent::$FOR_OTHERS);

    }
    
}