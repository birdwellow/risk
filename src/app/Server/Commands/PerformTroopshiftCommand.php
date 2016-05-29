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
class PerformTroopshiftCommand extends AbstractGameFlowControllerCommand {
    
    public function perform(SocketEvent $event, Match $match){
        
        $startRegion = $event->moveStart;
        $endRegion = $event->moveEnd;
        $shiftTroops = $event->shiftTroops;
        
        $roundPhaseData = json_decode($match->roundphasedata);
        
        if ($startRegion->troops - $shiftTroops < 1){
            Log::error('Remaining troops in start region are less than 1');
            return;
        } else if(isset($roundPhaseData->shiftedTroops) && $roundPhaseData->shiftedTroops) {
            Log::error('Troops already shifted');
            return;
        } else {
            
            $startRegion->troops -= $shiftTroops;
            $endRegion->troops += $shiftTroops;
            
            $event->moveEndTroops = $endRegion->troops;
            $event->moveStartTroops = $startRegion->troops;
            
            $startRegion->save();
            $endRegion->save();
            
            $roundPhaseData = json_decode($match->roundphasedata);
            $roundPhaseData->shiftedTroops = true;
        
            return new ServerEvent("troopshift.result", $event->getData(), $match);

        }
    }
    
}