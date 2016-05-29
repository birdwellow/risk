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
    
    private $duplicateAllowedEvents = [
            "get.init.data",
            "new.chat.message",
            "player.connect",
            "player.disconnect",
            "deploy.unit"
        ];
    
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
            \Illuminate\Support\Facades\Log::info(
                    'Checking: ' . $event->getName() . ' in ' . implode(", ", $this->duplicateAllowedEvents) . '?');
            if(!in_array($event->getName(), $this->duplicateAllowedEvents)) {
                \Illuminate\Support\Facades\Log::info('-> no');
                throw new Exception($event->getName() . " has been sent more than once subsequently!");
            } else {
                \Illuminate\Support\Facades\Log::info('-> yes, allowed');
            }
        }
        
    }

}
