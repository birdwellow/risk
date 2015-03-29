<?php

namespace Game\Server;

use Game\Contracts\GameServerInterface;
use Illuminate\Support\Facades\Log;
use Game\Model\Match;
use SplObjectStorage;
use Ratchet\ConnectionInterface;
use Game\User;

/**
 * Description of GameServer
 *
 * @author fvo
 */
class GameServer implements GameServerInterface {
    
    protected $matches = array();
    protected $connections = array();
    
    protected function getMatchById($id){
        foreach (array_keys($this->connections) as $connId){
            $connectionField = $this->connections[$connId];
            if($connectionField["match"]->id == $id){
                return $connectionField["match"];
            }
        }
    }

    public function __construct() {
        $this->matches = new \SplObjectStorage;
    }
    
    protected function hasMatchById($id){
        return $this->getMatchById($id) !== null;
    }

    public function onOpen(ConnectionInterface $conn) {
        Log::info("open");
    }
    
    public function onClose(ConnectionInterface $conn) {
        Log::info("Closing from " . sizeof($this->connections) . " connections");
        foreach(array_keys($this->connections) as $connId){
            $connectionField = $this->connections[$connId];
            if($connectionField["conn"] == $conn){
                $user = $connectionField["user"];
                $match = $connectionField["match"];
                $match->disconnectUser($user);
                $user->setSocket(null);
                unset($this->connections[$connId]);
                Log::info("Disconnected user " . $user->name . " from match " . $match->name);
            }
        }
        Log::info(sizeof($this->connections) . " connections remaining");
    }
    
    public function onError(ConnectionInterface $conn, \Exception $e) {
        Log::info("error " . $e->getMessage() . " in " . $e->getFile() . ", line " . $e->getLine());
    }
    
    public function onMessage(ConnectionInterface $conn, $msg) {
        Log::info("message " . (string)$msg);
        
        $message = json_decode($msg);
        if($message->type == "chat.message"){
            var_dump($message);
            $connectionId = $message->connectionid;
            if($connectionId !== null && $this->connections[$connectionId] !== null){
                $connectionField = $this->connections[$connectionId];
                if($conn == $connectionField["conn"]){
                    $match = $connectionField["match"];
                    $user = $connectionField["user"];
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
                    Log::info("New chate message from user " . $user->name . " in match " . $match->name);
                } else {
                    Log::info("Nothing");
                }
            }
        } else if($message->type == "join"){
            
            $match = $this->getMatchById($message->data->match_id);
            if($match == null){
                $match = Match::find($message->data->match_id);
            }
            $user = User::find($message->data->user_id);
            
            if($match !== null && $user !== null){
                
                $user->setSocket($conn);
                $match->connectUser($user);
                
                $connection = array(
                    "conn" => $conn,
                    "user" => $user,
                    "match" => $match
                );
                $connectionId = uniqid();
                $this->connections[$connectionId] = $connection;
                
                $conn->send(
                    json_encode(
                        [
                            "type" => "connectionid",
                            "data" => $connectionId
                        ]
                    )
                );
            }
        }
    }
    
}
