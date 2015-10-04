<?php

namespace Game\Server;

use Game\Contracts\GameServerInterface;
use Game\Server\SessionData;
use Game\User;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

use Ratchet\ConnectionInterface;

use SplObjectStorage;

/**
 * Description of GameServer
 *
 * @author fvo
 */
class GameServer implements GameServerInterface {
    
    protected $sessions;
    protected $matches;
    protected $events;

    public function __construct() {
        $this->sessions = new SplObjectStorage();
        $this->matches = new SplObjectStorage();
        $this->events = array();
        
        $this->on("get.all", function($sessionData){
            
            $this->sendAllData($sessionData);
        });
        
        $this->on("select.region", function($sessionData, $data){
            $match = $sessionData->getMatch();
            $user = $sessionData->getUser();
            foreach($match->getConnectedUsers() as $connectedUser){
                if($user !== $connectedUser){
                    Log::info("Forwarding to " . $user->name);
                    $connectedUser->getSocket()->send(
                        json_encode(
                            [
                                "type" => "select.region",
                                "data" => "[regions:id=" . $data . "]",
                                "username" => $user->name
                            ]
                        )
                    );
                }
            }
        });
        
        /*
        $this->on("chat.message", function($conn, $message){
            $this->chatMessage($conn, $message);
        });
        */
    }
    
    
    public function on($eventName, $callback){
        
        $this->events[$eventName] = $callback;
        
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
        
        $joinId = $conn->WebSocket->request->getQuery()->get("joinid");
        $this->connectUser($conn, $joinId);
        
    }
    
    public function onClose(ConnectionInterface $conn) {
        
        $this->disconnect($conn);
        
    }
    
    public function onError(ConnectionInterface $conn, \Exception $e) {
        Log::info("error " . $e->getMessage() . " in " . $e->getFile() . ", line " . $e->getLine());
    }
    
    public function onMessage(ConnectionInterface $conn, $msg) {
        
        $message = json_decode($msg);
        $sessionData = $this->sessions[$conn];
        
        if(isset($this->events[$message->type])){
            $callback = $this->events[$message->type];
            if(!isset($message->data)){
                $message->data = null;
            }
            $callback($sessionData, $message->data);
        }
    }
    
    
    protected function connectUser(ConnectionInterface $conn, $joinId){
        
        $user = User::where("joinid", $joinId)->first();
        if(!$user instanceof User){
            return;
        }
        if($user->joinid !== $joinId){
            return;
        }
        
        $user->joinid = null;
        $user->save();
        
        $joinedMatch = $user->joinedMatch;
        
        if(!$this->sessions->contains($conn)){
        
            $match = $this->getMatchById($joinedMatch->id);
            if($match == null){
                $match = $joinedMatch;
            }

            if($match !== null && $user !== null){

                $match->connectUser($user);
                $user->setSocket($conn);

                $data = new SessionData(
                    $user,
                    $match,
                    $conn
                );
                $this->sessions[$conn] = $data;
                $this->matches->attach($match);
            }
            
        }
            
    }

    
    protected function disconnect(ConnectionInterface $conn){
        
        if($this->sessions->contains($conn)){
        
            Log::info("Closing from " . count($this->sessions) . " connections and " . count($this->matches) . " matches.");
        
            $data = $this->sessions[$conn];
            $user = $data->getUser();
            $match = $data->getMatch();
            
            $match->disconnectUser($user);
            $user->disconnect();
            $this->cleanUp($conn);
            
            if( count($match->getConnectedUsers()) == 0 ){
                $this->matches->detach($match);
            }
            
            Log::info(count($this->sessions) . " connections remaining and " . count($this->matches) . " matches remaining.");
        }
        
    }

    /*
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
    */

    
    protected function sendAllData(SessionData $session){
        
        $match = $session->getMatch();
        $socket = $session->getSocket();
        $user = $session->getUser();
        
        App::setLocale($user->language);
        foreach($match->continents as $continent){
            foreach($continent->regions as $region){
                $key = 'match.region.' . $region->name;
                $region->name = Lang::get($key);
            }
        }
        
        $socket->send(
            json_encode(
                [
                    "type" => "get.all",
                    "data" => $match
                ]
            )
        );
        
    }
    
    protected function cleanUp(ConnectionInterface $conn){
        
        $deleteConnections = new SplObjectStorage();
        $deleteConnections[$conn] = $this->sessions[$conn];
        $this->sessions->removeAll($deleteConnections);
        
    }
    
}
