<?php

namespace Game\Server\Commands;

use Cmgmyr\Messenger\Models\Message;

use Illuminate\Support\Facades\Log;

use Game\Server\SocketEvent;
use Game\Server\ServerEvent;
use Game\Model\Match;

/**
 * Description of PerformAttackCommand
 *
 * @author birdwellow
 */
class NewChatMessageCommand extends AbstractGameFlowControllerCommand {
    
    public function perform(SocketEvent $event, Match $match){
        
        $user = $event->getUser();
        $thread = $match->thread;
        
        Message::create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'body' => $event->text,
        ]);
        $thread->markAsRead($user->id);
        
        Log::info("Saved: " . $event->text);
        
        $event->user = $user;
        
        return new ServerEvent("new.chat.message", $event->getData(), $match);

    }
    
}