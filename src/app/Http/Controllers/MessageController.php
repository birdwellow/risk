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

    
    
    public function newThreadWithNewMessageForm() {

            return view("message.init");
            
    }

    
    
    public function newThreadWithNewMessageForUserForm($userid) {

            $user = $this->userManager->getUserForId($userid);
            return view("message.init")
                ->with("user", $user)
                ->with("messageTitle", "")
                ->with("reuseOldThread", true);
            
    }
    

    
    public function newMessageInThread($threadId) {
        
            $thread = $this->messageManager->getThreadForUser(Auth::user(), $threadId);

            $messageText = trim(Input::get("thread_message_text"));
            $this->check([
                    "thread_message_text" => $messageText
                ], "MESSAGE.NOT.SENT");
            
            $this->messageManager->newMessage(
                    $thread,
                    Auth::user(),
                    $messageText
            );

            return redirect()->back();
        
    }
    
    

    public function createNewThreadWithNewMessage() {

            $threadSubject = trim(Input::get('thread_subject'));
            $userNameArray = explode(",", Input::get('thread_recipients'));
            $recipients = $this->userManager->findUsersForNames($userNameArray);

            $attributes = [
                "thread_subject" => $threadSubject,
                "thread_recipients" => sizeof($recipients),
            ];
            $this->check($attributes, "THREAD.NOT.CREATED");
                
            $thread = $this->messageManager->newThread(
                    $threadSubject,
                    Auth::user(),
                    $recipients,
                    Input::get('thread_reuseexistingthread')
            );

            if(trim(Input::get('thread_message_text'))){
                $this->messageManager->newMessage(
                        $thread,
                        Auth::user(),
                        trim(Input::get('thread_message_text'))
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

            $attributes = [
                "thread_recipients" => sizeof($userArray),
            ];
            $this->check($attributes, "USERS.NOT.ADDED.TO.THREAD");

            $this->messageManager->addUsersToThread($user, $userArray, $thread);

            return redirect()->route('thread.allmessages', $thread->id);
        
    }
    
    
    
    public function loadThreadPart($threadId){
        
            $thread = $this->messageManager->getThreadForUser(Auth::user(), $threadId);
            return view("htmlpart.thread")
                            ->with('thread', $thread);
        
    }

}
