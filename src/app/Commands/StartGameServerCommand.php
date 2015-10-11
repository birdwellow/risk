<?php

namespace Game\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log as Log;
use \Game\Contracts\GameServerInterface;
use \Ratchet\WebSocket\WsServer;
use \Ratchet\Http\HttpServer;
use \Ratchet\Server\IoServer;

/**
 * Description of StartGameServerCommand
 *
 * @author fvo
 */
class StartGameServerCommand extends Command {
    
    protected $name = "gameserver:start";
    protected $description = "Start the Game Server.";
    protected $gameServer;


    public function __construct(GameServerInterface $gameServer) {
        parent::__construct();
        $this->gameServer = $gameServer;
    }
    
    
    public function fire(){
        Log::info("StartGameServerCommand called");
        
        $port = 7778;
        
        $wsServer = new WsServer($this->gameServer);
        $httpServer = new HttpServer($wsServer);
        $server = IoServer::factory($httpServer, $port);
        
        $this->line("<info>Listening on port</info> <comment>" . $port . "</comment>");
        
        $server->run();
    }
    
    
    protected function getOptions(){
        return [];
    }
}
