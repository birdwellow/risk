<?php

namespace Game\Server;

use Game\User;
use Game\Model\Match;

use Ratchet\ConnectionInterface;

/**
 * Description of SessionData
 *
 * @author birdwellow
 */
class SessionData {
    
    protected $user;
    protected $match;
    protected $conn;
    
    public function __construct(User $user, Match $match, ConnectionInterface $conn) {
        $this->user = $user;
        $this->match = $match;
        $this->conn = $conn;
    }


    public function getUser(){
        return $this->user;
    }
    
    public function getMatch(){
        return $this->match;
    }
    
    public function getSocket(){
        return $this->conn;
    }
    
    public function refresh(){
        $this->user = $this->user->fresh();
        $this->match = $this->match->fresh();
    }
    
}
