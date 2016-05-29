<?php

namespace Game\Server;

use Illuminate\Support\Facades\Log;

use Game\Server\Filters\FilterInterface;
use Game\Server\Filters\DuplicateEventFilter;
use Game\Server\Filters\AbortiveEventFilter;

use Game\Model\Match;

/**
 * Description of GameFlowController
 *
 * @author birdwellow
 */
class GameFlowController {
    
    protected $eventMap;
    protected $filters = [];
    
    public function __construct() {
        
        $this->eventMap = [
            "get.init.data" => "SendInitialDataCommand",
            "attack.confirm" => "PerformAttackCommand",
            "attack.troopshift.confirm" => "PerformTroopshiftAfterAttackCommand",
            "new.chat.message" => "NewChatMessageCommand",
            "player.connect" => "UserConnectCommand",
            "player.disconnect" => "UserDisconnectCommand",
            "deploy.unit" => "DeployUnitCommand",
            "troopshift.confirm" => "PerformTroopshiftCommand",
            "trade.cards" => "TradeCardsCommand",
            
            "troopgain.finish" => "FinishTroopGainCommand",
            "troopdeployment.finish" => "FinishTroopDeploymentCommand",
            "attack.finish" => "FinishAttackCommand",
            "phase.finish" => "FinishPhaseCommand",
        ];
        
        $this->addFilter(new DuplicateEventFilter());
    }
    
    public function processSocketEvent(SocketEvent $event, Match $match){
        
        $eventKey = $event->getName();
        if(isset($this->eventMap[$eventKey])){
            
            $this->filterBeforeProcessing($event, $match);
            
            $commandName = "Game\\Server\\Commands\\" . $this->eventMap[$eventKey];
            $command = new $commandName();
            $result = $command->perform($event, $match);
            if($result){
                Log::info("Result is '" . $result->getName() . "'");
            }
            
            $this->filterAfterProcessing($event, $match);
            
            return $result;
            
        } else {
            //Log::warn("Event key '" . $eventKey . "' not defined");
        }
        
    }
    
    private function addFilter(FilterInterface $filter){
        array_push($this->filters, $filter);
    }
    
    public function clear(Match $match){
        foreach ($this->filters as $filter) {
            $filter->clear($match);
        }
    }
    
    private function filterBeforeProcessing(SocketEvent $event, Match $match){
        
        try {
            foreach ($this->filters as $filter) {
                $filter->filterBeforeProcessing($event, $match);
            }
        } catch (Exception $exception) {
            Log::error("Filter error: " . $exception->getMessage());
            return;
        }
        
    }
    
    private function filterAfterProcessing(SocketEvent $event, Match $match){
        
        try {
            foreach ($this->filters as $filter) {
                $filter->filterAfterProcessing($event, $match);
            }
        } catch (Exception $exception) {
            Log::error("Filter error: " . $exception->getMessage());
            return;
        }
        
    }
    
}
