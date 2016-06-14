<?php

namespace Game\Server\Commands;

use Illuminate\Support\Facades\Log;

use Game\Server\SocketEvent;
use Game\Server\ServerEvent;

use Game\Managers\MatchManager;

use Game\Model\Match;
use Game\Model\Continent;
use Game\User;

/**
 * Description of PerformAttackCommand
 *
 * @author birdwellow
 */
class PerformAttackCommand extends AbstractGameFlowControllerCommand {
    
    public function perform(SocketEvent $event, Match $match){
        
        $data = $event->getData();
        
        $attackorRegion = $event->moveStart;
        $attackorTroops = min($attackorRegion->troops-1, 3);
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
            
            $autoShiftTroops = ceil($attackorRegion->troops / 2);
            $autoShiftTroops = min($autoShiftTroops, $attackorRegion->troops);
            
            $defenderRegion->troops += $autoShiftTroops;
            $attackorRegion->troops -= $autoShiftTroops;
            $defenderRegion->save();
            $attackorRegion->save();
            
            $roundPhaseData = $match->roundphasedata;
            if(!$roundPhaseData){
                $roundPhaseData = new \stdClass();
                $roundPhaseData->conqueredregions = 0;
            } else {
                if(!is_object($roundPhaseData)){
                    $roundPhaseData = json_decode($roundPhaseData);
                }
                if(!isset($roundPhaseData->conqueredregions)){
                    $roundPhaseData->conqueredregions = 0;
                }
            }
            $roundPhaseData->conqueredregions++;
            $match->roundphasedata = json_encode($roundPhaseData, JSON_NUMERIC_CHECK);
            $event->roundphasedata = json_encode($roundPhaseData, JSON_NUMERIC_CHECK);
            $match->save();
            
            $event->loser = $this->kickLoser($match->fresh());
            
            $winner = $this->calculateWinner($match->fresh());
            if($winner !== null){
                $this->endMatch($match, $winner);
                $event->winner = $winner;
                
                // Do not update continents, as they have been deleted by endMatch()
                return new ServerEvent($eventName, $event->getData(), $match);
            }
            
        }
        
        $this->updateContinents($match);
        
        $event->continent = $defenderRegion->fresh()->continent;
                
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
    
    
    protected function updateContinents(Match $match) {
        
        Log::info('Updating continents');
        
        foreach ($match->continents as $continent) {
            $owner = $this->calculateContinentOwner($continent);
            if($owner !== null){
                Log::info('Continent ' . $continent->name . ' belongs to ' . $owner->name);
                $continent->owner()->associate($owner);
            } else {
                Log::info('Continent ' . $continent->name . ' belongs to nobody');
                $continent->owner()->dissociate();
            }
            $continent->save();
        }
        
    }
    
    
    protected function calculateContinentOwner(Continent $continent){
        
        $owner = $continent->regions->first()->owner;
        
        foreach ($continent->regions as $region){
            if($region->owner->id !== $owner->id){
                return null;
            }
        }
        
        return $owner;
        
    }
    
    
    protected function kickLoser(Match $match) {
        
        foreach ($match->joinedUsers as $player){
            if(count($player->regions) == 0){
                $player->joinedMatch()->dissociate();
                $player->matchnotification = 'match:lost';
                $player->save();
                
                foreach($player->cards as $card){
                    $card->cardOwner()->dissociate();
                    $card->save();
                }
                return $player;
            }
        }
        
        return null;
        
    }
    
    
    protected function calculateWinner(Match $match) {
        
        $totalRegions = count($match->regions);
        
        foreach ($match->joinedUsers as $player){
            if(count($player->regions) == $totalRegions){
                return $player;
            }
        }
        
        return null;
        
    }
    
    
    protected function endMatch(Match $match, User $winner) {
        
        $winner->matchnotification = 'match:won';
        $winner->matcheswon += 1;
        $winner->save();
        $matchManager = new MatchManager();
        $matchManager->endMatch($match);
        
    }
    
}