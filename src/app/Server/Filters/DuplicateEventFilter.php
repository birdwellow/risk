<?php

namespace Game\Server\Filters;

use \Exception;

use Game\Server\Filters\FilterInterface;
use Game\Model\Match;
use Game\Server\SocketEvent;

/**
 * Description of BruteForceFilter
 *
 * @author birdwellow
 */
class DuplicateEventFilter implements FilterInterface {
    
    private $matchLastEvents;
    
    public function __construct() {
        
        $this->matchLastEvents = array();
        
    }
    
    
    public function filterBeforeProcessing(SocketEvent $event, Match $match){
        
        $matchKey = "match:id=".$match->id;
        $this->check($matchKey, $event);
        // Substituting $this->matchEventHistories[$matchKey] is not possible!!??!
        $this->matchLastEvents[$matchKey] = $event;
        
    }
    
    
    public function filterAfterProcessing(SocketEvent $event, Match $match){
        
        // nothing to do here
        
    }
    
    
    public function clear(Match $match){
        
        $matchKey = "match:id=".$match->id;
        unset($this->matchLastEvents[$matchKey]);
        
    }
    
    
    private function check($matchKey, SocketEvent $event){
        
        if(!isset($this->matchLastEvents[$matchKey])){
            return;
        }
        $lastEvent = $this->matchLastEvents[$matchKey];
        if($event->getName() == $lastEvent->getName()){
            if($event->getName() == "trade.cards") {
                throw new Exception("trade.cards sent too often!!");
            }
        }
        
    }

}
