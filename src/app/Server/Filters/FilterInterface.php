<?php

namespace Game\Server\Filters;

use Game\Model\Match;
use Game\Server\SocketEvent;

/**
 *
 * @author birdwellow
 */
interface FilterInterface {
    
    public function doFilter(SocketEvent $event, Match $match);
    
}
