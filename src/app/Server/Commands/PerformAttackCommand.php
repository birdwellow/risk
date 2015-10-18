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
        $data->result = [
            [1,2,3],
            [4,5]
        ];
        
        return new ServerEvent("attack.perform", $event->getData(), $match);
    }
    
}