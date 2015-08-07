<?php

namespace Game\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

use Game\Managers\MessageManager;
use Game\Managers\UserManager;

class MessageController extends Controller {
    
        
    protected $messageManager;
    protected $userManager;


    
    public function __construct(MessageManager $messageManager, UserManager $userManager) {
        
            $this->middleware('auth');
            $this->messageManager = $messageManager;
            $this->userManager = $userManager;
            
    }

    
    
    public function initNewThreadWithNewMessage() {

            return view("message.init");
            
    }
    

    
    public function newMessageInThread($threadId) {
        
            $thread = $this->messageManager->getThreadForUser(Auth::user(), $threadId);

            $this->messageManager->newMessage(
                    $thread,
                    Auth::user(),
                    Input::get("thread_message_text")
            );

            return redirect()->back();
        
    }
    
    

    public function newThreadWithNewMessage() {

            $userNameArray = explode(",", Input::get('thread_recipients'));
            $recipients = $this->userManager->findUsersForNames($userNameArray);

            $thread = $this->messageManager->newThread(
                    Input::get('thread_subject'),
                    Auth::user(),
                    $recipients,
                    Input::get('thread_reuseexistingthread')
            );

            if(trim(Input::get('thread_message_text'))){
                $this->messageManager->newMessage(
                        $thread,
                        Auth::user(),
                        Input::get('thread_message_text')
                );
            }

            return redirect()->route('thread.allmessages', $thread->id);
            
    }
    

    
    public function showAllThreads() {

            $threads = $this->messageManager->getThreadsForUser(Auth::user());

            $thread = $threads->first();
            if($thread !== null) {
                    $thread->markAsRead(Auth::user()->id);
            }

            return view("message.all")
                            ->with('threads', $threads)
                            ->with('thread', $thread);
            
    }
    

    
    public function showThread($threadId) {

            $threads = $this->messageManager->getThreadsForUser(Auth::user());

            $thread = $threads->first();
            
            $this->messageManager->getThreadForUser(Auth::user(), $threadId);

            return view("message.all")
                            ->with('threads', $threads)
                            ->with('thread', $thread);
            
    }
    
    
    
    public function addUsers($threadId) {
        
            $user = Auth::user();
            $thread = $this->messageManager->getThreadForUser(Auth::user(), $threadId);
            
            $userNameArray = explode(",", Input::get("thread_recipients"));
            $userArray = $this->userManager->findUsersForNames($userNameArray);

            $this->messageManager->addUsersToThread($user, $userArray, $thread);

            return redirect()->route('thread.allmessages', $thread->id);
        
    }
    
    
    
    public function loadThreadPart($threadId){
        
            $thread = $this->messageManager->getThreadForUser(Auth::user(), $threadId);
            return view("htmlpart.thread")
                            ->with('thread', $thread);
        
    }

}
