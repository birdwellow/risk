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
        
        App::setLocale($event->getUser()->language);
        foreach($match->continents as $continent){
            foreach($continent->regions as $region){
                $key = 'match.region.' . $region->name;
                $region->name = Lang::get($key);
            }
        }
        
        $serverEvent = new ServerEvent("get.all", $match, $match, ServerEvent::$FOR_SELF);
        return $serverEvent;
    }
    
}