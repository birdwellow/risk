<?php

namespace Game\Server\Filters;

use Game\Model\Match;
use Game\Server\SocketEvent;

/**
 *
 * @author birdwellow
 */
interface FilterInterface {
    
    public function filterBeforeProcessing(SocketEvent $event, Match $match);
    
    public function filterAfterProcessing(SocketEvent $event, Match $match);
    
    public function clear(Match $match);
    
}
