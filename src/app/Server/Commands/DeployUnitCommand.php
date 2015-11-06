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
class DeployUnitCommand extends AbstractGameFlowControllerCommand {
    
    public function perform(SocketEvent $event, Match $match){
        
        $user = $event->getUser();
        $region = $event->region;
        
        if($user->id == $region->owner->id && $user->newtroops > 0){
        
            $region->troops += 1;
            $region->save();
            
            $user->newtroops -= 1;
            $user->save();
            
            $event->player = $user;
            $event->newRegionTroops = $region->troops;
            $event->newPlayerTroops = $user->newtroops;
            
            return new ServerEvent("unit.deployed", $event->getData(), $match);
    
        }
        
    }
    
}