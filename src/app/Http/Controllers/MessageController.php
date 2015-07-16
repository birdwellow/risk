<?php

namespace Game\Http\Controllers;

use Cmgmyr\Messenger\Models\Thread;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Game\User;
use Carbon\Carbon;

class MessageController extends Controller {

    public function __construct() {

        $this->middleware('auth');
    }

    public function initNewMessage() {

        return view("message.init");
    }

    public function sendNewThreadMessage($threadId) {

        Message::create([
            'thread_id' => $threadId,
            'user_id' => Auth::user()->id,
            'body' => Input::get("message"),
        ]);

        return redirect()->back();
    }

    public function sendNewMessage() {

        $input = Input::all();
            Log::info($input);

        $recipientIds = array();
        array_push($recipientIds, Auth::user()->id);
        if (Input::has('usernames')) {
            $recipientNames = explode(",", $input['usernames']);
            Log::info($recipientNames);

            foreach ($recipientNames as $recipientName) {
                $foundUser = User::where("name", $recipientName)->first();
                if ($foundUser) {
                    array_push($recipientIds, $foundUser->id);
                }
            }
        }
        $thread = $this->findThreadForUserIds($recipientIds);
        
        if($thread == null){

            $thread = Thread::create([
                'subject' => $input['subject'],
            ]);

            Participant::create([
                'thread_id' => $thread->id,
                'user_id' => Auth::user()->id,
                'last_read' => new Carbon
            ]);

            $thread->addParticipants($recipientIds);
            
        }

        Message::create([
            'thread_id' => $thread->id,
            'user_id' => Auth::user()->id,
            'body' => $input['message'],
        ]);

        return redirect()->route('all.messages');
    }

    public function showAllThreads() {

        // All threads, ignore deleted/archived participants
        $threads = Thread::getAllLatest();

        // All threads that user is participating in
        // $threads = Thread::forUser(Auth::user()->id);//->latest('updated_at')->get();
        // All threads that user is participating in, with new messages
        // $threads = Thread::forUserWithNewMessages(Auth::user()->id);//->latest('updated_at')->get();

        return view("message.all")
                        ->with('threads', $threads);
    }

    public function showAllThreadMessages($threadId) {

        $thread = Thread::find($threadId);
        $thread->markAsRead(Auth::user()->id);

        return view("message.thread")
                        ->with('thread', $thread);
    }

    protected function findThreadForUserIds($recipientIds) {
        
        if(sizeof($recipientIds) > 0){
            $possibleCommonThreadIdsPerUser = array();
            foreach ($recipientIds as $recipientId) {

                $recipientUser = User::find($recipientId);
                $recipientThreadIds = array();
                foreach ($recipientUser->threads as $thread) {
                    array_push($recipientThreadIds, $thread->id);
                }
                array_push($possibleCommonThreadIdsPerUser, $recipientThreadIds);
            }
            
            $possibleCommonThreadIds = $possibleCommonThreadIdsPerUser[0];
            foreach ($possibleCommonThreadIdsPerUser as $possibleCommonThreadIdsForUser){
                $possibleCommonThreadIds = array_intersect($possibleCommonThreadIds, $possibleCommonThreadIdsForUser);
            }
            
            $recipientNumber = count($recipientIds);
            foreach ($possibleCommonThreadIds as $possibleCommonThreadId){
                $thread = Thread::find($possibleCommonThreadId);
                $threadParticipantNumber = count($thread->participants);
                if($threadParticipantNumber == $recipientNumber){
                    return $thread;
                }
                
            }
        }
        
        return null;
    }

}
