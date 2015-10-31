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
class PerformTroopshiftAfterAttackCommand extends AbstractGameFlowControllerCommand {
    
    public function perform(SocketEvent $event, Match $match){
        
        $startRegion = $event->moveStart;
        $endRegion = $event->moveEnd;
        $shiftTroops = $event->shiftTroops;
        
        if($startRegion->troops - $shiftTroops >= 1){
            
            $startRegion->troops -= $shiftTroops;
            $endRegion->troops += $shiftTroops;
            
            $event->moveEndTroops = $endRegion->troops;
            $event->moveStartTroops = $startRegion->troops;
            
            $startRegion->save();
            $endRegion->save();
        
            return new ServerEvent("troopshift.after.attack.result", $event->getData(), $match);

        }
    }
    
}