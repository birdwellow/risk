<?php

namespace Game\Server;

use Game\Contracts\GameServerInterface;
use Game\Server\SessionData;
use Game\User;
use Game\Model\Match;

use Illuminate\Support\Facades\Log;

use Ratchet\ConnectionInterface;

use SplObjectStorage;
use Exception;

/**
 * Description of GameServer
 *
 * @author fvo
 */
class GameServer implements GameServerInterface {
    
    /*
     * $sessions[$connectionInterface] => $sessionData
     */
    protected $sessions;
    
    /*
     * $matchSessions["id=xxx"] => [
     *      "id=yyy" => $sessionData1,
     *      ...
     * ]
     */
    protected $matchSessions;
    
    protected $gameFlowController;

    public function __construct() {
        $this->gameFlowController = new GameFlowController();
        
        $this->sessions = new SplObjectStorage();
        $this->matchSessions = array();
    }
    

    public function onOpen(ConnectionInterface $conn) {
        
        $this->connect($conn);
        
        $jsonData = json_encode([
            "type" => "player.connect"
        ]);
        $this->onMessage($conn, $jsonData);
        
    }
    
    public function onClose(ConnectionInterface $conn) {
        
        $jsonData = json_encode([
            "type" => "player.disconnect"
        ]);
        $this->onMessage($conn, $jsonData);
        
        $this->disconnect($conn);
        
    }
    
    public function onError(ConnectionInterface $conn, Exception $e) {
        
        Log::error(get_class($this) . ": Error " . $e->getMessage() . " in " . $e->getFile() . ", line " . $e->getLine());
        
    }
    
    public function onMessage(ConnectionInterface $conn, $messageJson) {

        $session = $this->sessions[$conn];
        $session->refresh();
        
        $socketEvent = new SocketEvent($session, $messageJson);
        
        $serverEvent = $this->gameFlowController->processSocketEvent($socketEvent, $session->getMatch());
        
        if($serverEvent instanceof ServerEvent){
            $this->sendServerEvent($serverEvent, $socketEvent->getUser());
        }
        
    }
    
    
    protected function sendServerEvent(ServerEvent $serverEvent, User $originalSender){

        
        $match = $serverEvent->getMatch();

        $matchSessions = $this->matchSessions["match:id=".$match->id];
        
        foreach ($matchSessions as $session){

            $session->refresh();
            $eventReceiver = $session->getUser();

            $forAll = $serverEvent->isForAll();
            $forOthersAndReceiverIsNotSender = $serverEvent->isForAllOthers() && $eventReceiver->id !== $originalSender->id;
            $forSenderAndReceiverIsSender = $serverEvent->isForSender() && $eventReceiver->id == $originalSender->id;

            $mustSend = $forAll || $forOthersAndReceiverIsNotSender || $forSenderAndReceiverIsSender;
            
            if($mustSend){
                $socket = $session->getSocket();
                $json = $serverEvent->toJson();
                $socket->send($json);
            }
        }

    }




    protected function connect(ConnectionInterface $conn){
        
        $joinId = $conn->WebSocket->request->getQuery()->get("joinid");
        
        $user = $this->getUserForJoinId($joinId);
        $match = $user->joinedMatch;
        
        $this->addConnection($user, $match, $conn);
        $this->dumpData();
            
    }

    
    protected function disconnect(ConnectionInterface $conn){
    
        $this->removeConnection($conn);
        $this->dumpData();
        
    }
    
    
    protected function getUserForJoinId($joinId) {
        
        $user = User::where("joinid", $joinId)->first();
        if(!$user instanceof User){
            throw new Exception("No user found for join ID $joinId");
        }
                
        $user->joinid = null;
        $user->save();
        
        return $user;
    }
    
    
    protected function addConnection(User $user, Match $match, ConnectionInterface $conn) {
        
        if(!$this->sessions->contains($conn)){

                $session = new SessionData(
                    $user,
                    $match,
                    $conn
                );
                $this->sessions[$conn] = $session;
            
        }
        
        if (!isset($this->matchSessions["match:id=" . $match->id])) {
            $this->matchSessions["match:id=" . $match->id] = array();
        }
        $this->matchSessions["match:id=".$match->id]["user:id=".$user->id] = $session;
        
    }
    
    
    protected function removeConnection(ConnectionInterface $conn) {
        
        $session = $this->sessions[$conn];
        $match = $session->getMatch();
        $user = $session->getUser();
        
        unset($this->sessions[$conn]);
        unset($this->matchSessions["match:id=".$match->id]["user:id=".$user->id]);
        
        if(count($this->matchSessions["match:id=".$match->id]) == 0){
            unset($this->matchSessions["match:id=".$match->id]);
            $this->gameFlowController->clearFilters($match);
        }
        
    }
    
    
    protected function dumpData() {
        
        $action = debug_backtrace()[1]['function'];
        $output = get_class($this) . "|" . strtoupper($action) . "[";
        $output .= "#sessions=" . count($this->sessions);
        $output .= "#matchSessions=" . count($this->matchSessions);
        $output .= "]";
        
        Log::info($output);
        
    }
    
}
