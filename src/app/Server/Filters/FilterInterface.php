<?php

namespace Game\Server\Filters;

use Game\Model\Match;
use Game\Server\SocketEvent;

/**
 *
 * @author birdwellow
 */
interface FilterInterface {
    
    public function filterIncomingEvent(SocketEvent $event, Match $match);
    
    public function filterOutgoingEvent(SocketEvent $event, Match $match);
    
    public function clear(Match $match);
    
}
