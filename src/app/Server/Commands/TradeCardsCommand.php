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
class TradeCardsCommand extends AbstractGameFlowControllerCommand {
    
    public function perform(SocketEvent $event, Match $match){
        
        $selectedCards = $this->mapObjectToArray($event->selectedCards);
        
        if(sizeof($selectedCards) !== 3){
            Log::error('Error, trying to trade ' . sizeof($selectedCards) . ' cards!');
            return;
        }
        
        $differentCardInstances = 
                ($selectedCards[0]->name !== $selectedCards[1]->name)
                && ($selectedCards[1]->name !== $selectedCards[2]->name)
                && ($selectedCards[2]->name !== $selectedCards[0]->name);
        if(!$differentCardInstances) {
            Log::error('The traded cards are not all different: '
                    . $selectedCards[0]->name . ", "
                    . $selectedCards[1]->name . ", "
                    . $selectedCards[2]->name);
                    return 0;
        }
        
        $newTroops = $this->getTroopsForCards($selectedCards);
        
        if($newTroops == 0){
            return;
        }
        
        $roundPhaseData = json_decode($match->roundphasedata);
        $roundPhaseData->trade = $newTroops;
        $roundPhaseDataJson = json_encode($roundPhaseData, JSON_NUMERIC_CHECK);
        $match->roundphasedata = $roundPhaseDataJson;
        $match->cardchangebonuslevel++;
        $match->save();
        
        $user = $event->getUser();
        $user->newtroops += $newTroops;
        $user->save();
        foreach ($selectedCards as $card) {
            $card->cardOwner()->dissociate();
            $card->save();
        }
        
        $event->newTroops = $user->newtroops;
        $event->roundphasedata = $roundPhaseData;
        
        return new ServerEvent("cards.traded", $event->getData(), $match);
        
    }
    
    
    protected function getTroopsForCards($cardsArray) {
        
        $sameType = 
                ($cardsArray[0]->cardunittype == $cardsArray[1]->cardunittype)
                && ($cardsArray[1]->cardunittype == $cardsArray[2]->cardunittype);
        
        $allDifferentTypes = 
                ($cardsArray[0]->cardunittype !== $cardsArray[1]->cardunittype)
                && ($cardsArray[1]->cardunittype !== $cardsArray[2]->cardunittype)
                && ($cardsArray[2]->cardunittype !== $cardsArray[0]->cardunittype);
        
        if($sameType){
            switch ($cardsArray[0]->cardunittype){
                case "1" : return 3;
                case "2" : return 5;
                case "3" : return 7;
            }
        } else if($allDifferentTypes){
            return 10;
        }
        
        return 0;
        
    }
    
    
    
    protected function mapObjectToArray($object) {
        
        $array = [];
        foreach ($object as $attributeValue){
            array_push($array, $attributeValue);
        }
        return $array;
        
    }
    
}