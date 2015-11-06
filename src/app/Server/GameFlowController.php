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
            "get.all" => "SendAllDataCommand",
            "attack.confirm" => "PerformAttackCommand",
            "troopshift.after.attack.confirm" => "PerformTroopshiftAfterAttackCommand",
            "new.chat.message" => "NewChatMessageCommand",
            "player.connect" => "UserConnectCommand",
            "player.disconnect" => "UserDisconnectCommand",
            "deploy.unit" => "DeployUnitCommand",
        ];
    }
    
    public function processSocketEvent(SocketEvent $event, Match $match){
        
        $eventKey = $event->getName();
        if(isset($this->eventMap[$eventKey])){
            $commandName = "Game\\Server\\Commands\\" . $this->eventMap[$eventKey];
            $command = new $commandName();
            $result = $command->perform($event, $match);
            return $result;
        } else {
            //Log::warn("Event key '" . $eventKey . "' not defined");
        }
        
    }
    
}
