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
class FinishPhaseCommand extends AbstractGameFlowControllerCommand {
    
    public function perform(SocketEvent $event, Match $match){
        
        $match->roundphase = "troopgain";
        $nextPlayer = $this->getNextPlayerForMatch($match);
        $newTroopsObject = $this->getNewTroopsObjectForUser($nextPlayer);
        foreach ($newTroopsObject as $newTroops) {
            $nextPlayer->newtroops += $newTroops;
        }
        $match->roundphasedata = json_encode($newTroopsObject);
        $match->activePlayer()->associate($nextPlayer);
        
        $match->save();
        $nextPlayer->save();
        
        $regionCard = $this->getRandomRegionsCard($match);
        if($regionCard){
            $user = $event->getUser();
            $regionCard->cardOwner()->associate($user);
            $user->save();
            $event->newCard = $regionCard;
        }
        
        $event->roundPhase = $match->roundphase;
        $event->roundphasedata = $match->roundphasedata;
        $event->ativePlayer = $match->activePlayer;
        $event->newTroops = $nextPlayer->newtroops;
        
        return new ServerEvent("phase.troopgain", $event->getData(), $match);
        
    }
    
    
    protected function getNextPlayerForMatch(Match $match) {
        
        $activePlayer = $match->activePlayer;
        $orderedPlayers = $match->joinedUsers->sortBy('matchorder');
        
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


    
    protected function getNewTroopsObjectForUser(\Game\User $player) {
        
        $newTroopsObject = new \stdClass();
        
        $newTroopsObject->base = 3;
        
        $regions = $player->regions;
        $newTroopsObject->regions = floor(count($regions));
        
        foreach ($player->continents as $continent){
            $continentName = $continent->name;
            $newTroopsObject->$continentName = $continent->troopbonus;
        }
        
        return $newTroopsObject;
        
    }
    
    
    protected function getRandomRegionsCard($match){
        
        $givenCards = Collection::make();
        
        foreach($match->joinedUsers as $player){
            $givenCards->merge($player->cards);
        }
        
        $ungivenCards = $this->getUngivenCards($givenCards, $match->regions);
        
        return $ungivenCards->random();
        
    }
    
    
    
    protected function getUngivenCards($givenCards, $allCards){
        
        $ungivenCards = Collection::make();
        
        foreach ($allCards as $card){
            if(!$this->isCardInStack($card, $givenCards)){
                $ungivenCards->push($card);
            }
        }
        
        return $ungivenCards;
        
    }
    
    
    protected function isCardInStack($card, $stack) {
        
        foreach ($stack as $stackCard) {
            if($card->id == $stackCard->id){
                return true;
            }
        }
        return false;
        
    }
    
}