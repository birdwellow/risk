<?php

namespace Game\Server;

use Illuminate\Support\Facades\Log;

use Game\Model\Match;

/**
 * Description of GameFlowController
 *
 * @author birdwellow
 */
class GameFlowController {
    
    protected $eventMap;
    
    public function __construct() {
        
        $this->eventMap = [
            "get.all" => "SendAllDataCommand"
        ];
    }
    
    public function processSocketEvent(SocketEvent $event, Match $match){
        
        $eventKey = $event->getName();
        $commandName = "Game\\Server\\Commands\\" . $this->eventMap[$eventKey];
        $command = new $commandName();
        $result = $command->perform($event, $match);
        
        return $result;
        
    }
    
}
