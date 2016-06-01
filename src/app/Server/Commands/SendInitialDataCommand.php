<?php

namespace Game\Server\Commands;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

use Illuminate\Support\Facades\Log;

use Game\Server\SocketEvent;
use Game\Server\ServerEvent;
use Game\Model\Match;

use Game\Managers\MessageManager;
use Game\Managers\UserManager;

/**
 * Description of SendAllDataCommand
 *
 * @author birdwellow
 */
class SendInitialDataCommand extends AbstractGameFlowControllerCommand {
    
    private $messageManager;

    public function __construct() {
        
        $userManager = new UserManager();
        $this->messageManager = new MessageManager($userManager);
        
    }
    
    public function perform(SocketEvent $event, Match $match){
        
        $match->me = "[players:id=" . $event->getUser()->id . "]";
        
        App::setLocale($event->getUser()->language);
        $match->translations = Lang::get("match");
        
        $thread = $this->messageManager->getThreadForUser(
                $event->getUser(),
                $match->thread->id);
        
        $messages = [];
        foreach ($thread->messages as $message){
            array_push($messages, [
                "user" => $message->user,
                "message" => $message->body
            ]);
        }
        $match->thread->messages = $messages;
        
        $serverEvent = new ServerEvent("init.data", $match, $match, ServerEvent::$FOR_SELF);
        return $serverEvent;
    }
    
}