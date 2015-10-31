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
class PerformAttackCommand extends AbstractGameFlowControllerCommand {
    
    public function perform(SocketEvent $event, Match $match){
        
        $data = $event->getData();
        
        $attackorRegion = $event->moveStart;
        $attackorTroops = min($attackorRegion->troops, 3);
        $defenderRegion = $event->moveEnd;
        $defenderTroops = min($defenderRegion->troops, 2);
        
        $attackerResults = $this->generateSortedRandomResults($attackorTroops);
        $defenderResults = $this->generateSortedRandomResults($defenderTroops);
        
        $data->attackResult = $this->arrangeRandomResults($attackerResults, $defenderResults);
        
        foreach ($data->attackResult as $resultPart) {
            if(isset($resultPart[0]) && $resultPart[0] == "win"){
                if($defenderRegion->troops >= 0){
                    $defenderRegion->troops--;
                } else {
                    $resultPart[0] = null;
                    $resultPart[1] = null;
                    $resultPart[2] = null;
                }
            } else if(isset($resultPart[0]) && $resultPart[0] == "lose"){
                if($attackorRegion->troops > 1){
                    $attackorRegion->troops--;
                } else {
                    $resultPart[0] = null;
                    $resultPart[1] = null;
                    $resultPart[2] = null;
                }
            }
            
        }
        $defenderRegion->save();
        $attackorRegion->save();
        
        $eventName = "attack.result";
        if($defenderRegion->troops == 0){
            $eventName = "attack.victory";
            $newOwner = $attackorRegion->owner;
            $defenderRegion->owner()->associate($newOwner);
            $defenderRegion->troops += 1;
            $attackorRegion->troops -= 1;
            $defenderRegion->save();
            $attackorRegion->save();
        }
        
        return new ServerEvent($eventName, $event->getData(), $match);
    }
    
    
    protected function generateSortedRandomResults($diceCount) {
        $result = array();
        for($i = 0; $i < $diceCount; $i++){
            $random = mt_rand(1, 6);
            array_push($result, $random);
        }
        rsort($result);
        return $result;
    }
    
    
    protected function arrangeRandomResults($attackerResults, $defenderResults) {
        $result = array();
        $maxLength = max([sizeof($attackerResults), sizeof($defenderResults)]);
        for($i = 0; $i < $maxLength; $i++){
            if(isset($attackerResults[$i]) && isset($defenderResults[$i])){
                $attackerWins = ($attackerResults[$i] > $defenderResults[$i] ? "win" : "lose");
                $currentResult = [$attackerWins, $attackerResults[$i], $defenderResults[$i]];
                array_push($result, $currentResult);
            } else {
                if(isset($defenderResults[$i])){
                    $currentResult = [null, null, $defenderResults[$i]];
                    array_push($result, $currentResult);
                }
                if(isset($attackerResults[$i])){
                    $currentResult = [null, $attackerResults[$i], null];
                    array_push($result, $currentResult);
                }
            }
        }
        return $result;
    }
    
    
    protected function processArrangedResults() {
        
    }
    
}