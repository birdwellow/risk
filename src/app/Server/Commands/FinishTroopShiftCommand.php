<?php

namespace Game\Server\Commands;

use Cmgmyr\Messenger\Models\Message;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

use Game\Server\SocketEvent;
use Game\Server\ServerEvent;
use Game\Model\Match;

/**
 * Description of PerformAttackCommand
 *
 * @author birdwellow
 */
class FinishTroopShiftCommand extends AbstractGameFlowControllerCommand {
    
    public function perform(SocketEvent $event, Match $match){
        
        $match->roundphase = "troopgain";
        $nextPlayer = $this->getNextPlayerForMatch($match);
        $nextPlayer->newtroops = $this->getNewTroopsForUser($nextPlayer);
        $match->activePlayer()->associate($nextPlayer);
        
        $match->save();
        $nextPlayer->save();
        
        $regionCard = $this->getRandomRegionsCard($match);
        
        $event->roundPhase = $match->roundphase;
        $event->ativePlayer = $match->activePlayer;
        $event->newTroops = $nextPlayer->newtroops;
        
        return new ServerEvent("phase.troopgain", $event->getData(), $match);
        
    }
    
    
    protected function getNextPlayerForMatch(Match $match) {
        
        $activePlayer = $match->activePlayer;
        $orderedPlayers = $match->sortBy('matchorder');
        
        $nextPlayer = $this->getFollowingPlayer($orderedPlayers, $activePlayer);
        if($nextPlayer == null){
            $nextPlayer = $orderedPlayers->first();
        }
        
        return $nextPlayer;
        
    }
    
    
    protected function getFollowingPlayer($orderedPlayers, $currentPlayer) {
        
        $take = false;
        
        foreach($orderedPlayers as $player){
            if($take){
                return $player;
            }
            if($player->id == $currentPlayer->id){
                $take = true;
            }
        }
        
        return null;
        
    }


    
    protected function getNewTroopsForUser(\Game\User $player) {
        
        $newTroops = 0;
        
        $regions = $player->regions;
        $newTroops += floor(count($regions));
        
        foreach ($player->continents as $continent){
            $newTroops += $continent->troopbonus;
        }
        
        return $newTroops;
        
    }
    
    
    protected function getRandomRegionsCard($match){
        
        $givenCards = Collection::make();
        
        foreach($match->joinedUsers as $player){
            $givenCards->merge($player->cards);
        }
        
        $ungivenCards = $givenCards->diff($match->regions);
        
        return $ungivenCards->random();
        
    }
    
}