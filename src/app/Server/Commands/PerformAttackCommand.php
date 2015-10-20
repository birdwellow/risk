<?php

namespace Game\Server\Commands;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
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
        
        $attackerResults = $this->generateSortedRandomResults(3);
        $defenderResults = $this->generateSortedRandomResults(2);
        
        $data->attackResult = $this->arrangeRandomResults($attackerResults, $defenderResults);
        
        return new ServerEvent("attack.perform", $event->getData(), $match);
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
    
}