<?php

namespace Game\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Description of AppServerServiceProvider
 *
 * @author fvo
 */
class GameServiceProvider extends ServiceProvider {

    protected $defer = true;
    
    public function register() {
        
        $this->app->bind("GameServer", function(){
            return new \Game\Server\GameServer();
        });
        $this->app->bind("StartGameServerCommand", function(){
            $server = $this->app->make("GameServer");
            return new \Game\Commands\StartGameServerCommand($server);
        });
        
        $this->commands("StartGameServerCommand");
        
    }
    
    public function provides() {
        return[
            "StartGameServerCommand"
        ];
    }
    
}
