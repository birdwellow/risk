<?php

namespace Game\Server\Commands;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

use Game\Server\SocketEvent;
use Game\Server\ServerEvent;
use Game\Model\Match;

/**
 * Description of SendAllDataCommand
 *
 * @author birdwellow
 */
class SendAllDataCommand extends AbstractGameFlowControllerCommand {
    
    public function perform(SocketEvent $event, Match $match){
        
        $match->me = "[players:id=" . $event->getUser()->id . "]";
        
        App::setLocale($event->getUser()->language);
        $match->translations = Lang::get("match");
        
        $serverEvent = new ServerEvent("get.all", $match, $match, ServerEvent::$FOR_SELF);
        return $serverEvent;
    }
    
}