<?php

namespace Game\Server;

use Game\Contracts\GameServerInterface;
use Illuminate\Support\Facades\Log;
use Game\Model\Match;
use Ratchet\ConnectionInterface;
use Game\User;
use SplObjectStorage;

/**
 * Description of GameServer
 *
 * @author fvo
 */
class GameServer implements GameServerInterface {
    
    protected $connections;
    protected $matches;

    public function __construct() {
        $this->connections = new SplObjectStorage();
        $this->matches = new SplObjectStorage();
    }
    
    
    protected function getMatchById($id){
        foreach ($this->matches as $match){
            if($match->id == $id) {
                return $match;
            }
        }
        return null;
    }
    

    public function onOpen(ConnectionInterface $conn) {
        
    }
    
    public function onClose(ConnectionInterface $conn) {
        
        $this->disconnect($conn);
        
    }
    
    public function onError(ConnectionInterface $conn, \Exception $e) {
        Log::info("error " . $e->getMessage() . " in " . $e->getFile() . ", line " . $e->getLine());
    }
    
    public function onMessage(ConnectionInterface $conn, $msg) {
        
        $message = json_decode($msg);
        
        if($message->type == "connect"){
            $this->connect($conn, $message);
        }
        else if($message->type == "chat.message"){
            $this->chatMessage($conn, $message);
        }
    }
    
    
    protected function connect(ConnectionInterface $conn, $message){
        
        if(!$this->connections->contains($conn)){
        
            $match = $this->getMatchById($message->data->match_id);
            if($match == null){
                $match = Match::find($message->data->match_id);
            }
            $user = User::find($message->data->user_id);

            if($match !== null && $user !== null){

                $match->connectUser($user);
                $user->setSocket($conn);

                $data = array(
                    "user" => $user,
                    "match" => $match
                );
                $this->connections[$conn] = $data;
                $this->matches->attach($match);
            }
            
        }
            
    }

    
    protected function disconnect(ConnectionInterface $conn){
        
        if($this->connections->contains($conn)){
        
            Log::info("Closing from " . count($this->connections) . " connections and " . count($this->matches) . " matches.");
        
            $data = $this->connections[$conn];
            $user = $data["user"];
            $match = $data["match"];
            
            $match->disconnectUser($user);
            $user->disconnect();
            $this->cleanUp($conn);
            
            if( count($match->getConnectedUsers()) == 0 ){
                $this->matches->detach($match);
            }
            
            Log::info(count($this->connections) . " connections remaining and " . count($this->matches) . " matches remaining.");
        }
        
    }

    
    protected function chatMessage(ConnectionInterface $conn, $message){
        
        $data = $this->connections[$conn];
        if($data !== null){
            $match = $data["match"];
            $user = $data["user"];
            foreach($match->getConnectedUsers() as $connectedUser){
                if($user !== $connectedUser){
                    $connectedUser->getSocket()->send(
                        json_encode(
                            [
                                "type" => "chat.message",
                                "data" => $message->data,
                                "username" => $user->name
                            ]
                        )
                    );
                }
            }
        }
        
    }
    
    protected function cleanUp(ConnectionInterface $conn){
        
        $deleteConnections = new SplObjectStorage();
        $deleteConnections[$conn] = $this->connections[$conn];
        $this->connections->removeAll($deleteConnections);
        
    }
    
}
