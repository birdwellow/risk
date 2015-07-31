<?php namespace Game\Managers;

use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Thread;
use Cmgmyr\Messenger\Models\Participant;

use Carbon\Carbon;

use Game\Managers\UserManager;
use Game\Exceptions\GameException;
use Game\Services\PolicyComplianceService;

use Illuminate\Support\Facades\Log;

/**
 * Description of MatchManager
 *
 * @author fvo
 */
class MessageManager {
    
    
        protected $validator;
        protected $userManager;


        public function __construct(PolicyComplianceService $validator, UserManager $userManager) {

                $this->validator = $validator;
                $this->userManager = $userManager;

        }


        protected function checkUserParticipantOfThread($user, $thread) {

                $participantsUserIds = $thread->participantsUserIds();
                foreach($participantsUserIds as $participantsUserId){
                    if($user->id == $participantsUserId){
                        return;
                    }
                }

                throw new GameException("USER.NOT.PARTICIPANT.OF.THREAD");

        }



        public function newMessage($thread, $user, $messageText) {

                $this->checkUserParticipantOfThread($user, $thread);
                
                $messageText = trim($messageText);
                $this->validator->check([
                        "message.text" => $messageText
                    ], "USER.MESSAGENOTSENT");

                return Message::create([
                    'thread_id' => $thread->id,
                    'user_id' => $user->id,
                    'body' => $messageText,
                ]);

        }


        public function newThread($subject, $creatorUser, $recipientUsersArray, $returnOldThreadIfExisting) {

                $attributes = [
                    "thread.subject" => $subject,
                    "thread.recipients" => sizeof($recipientUsersArray),
                ];
                $this->validator->check($attributes, "USER.THREADNOTCREATED");

                if($returnOldThreadIfExisting){

                    $matchingUsers = $recipientUsersArray;
                    array_push($matchingUsers, $creatorUser);
                    $thread = $this->findExistingThreadForRecipients($matchingUsers);
                    if($thread !== null) {
                        return $thread;
                    }

                }

                $thread = Thread::create([
                    'subject' => $subject
                ]);
                Participant::create([
                    'thread_id' => $thread->id,
                    'user_id' => $creatorUser->id,
                    'last_read' => new Carbon
                ]);

                $recipientUsersIdsArray = $this->userManager->extractUserIdsFromUsers($recipientUsersArray);
                $thread->addParticipants($recipientUsersIdsArray);

                return $thread;

        }


        public function findExistingThreadForRecipients($userArray) {

                $possibleCommonThreadIdsPerUser = array();
                foreach ($userArray as $user) {

                    $recipientThreadIds = array();
                    foreach ($user->threads as $thread) {
                        array_push($recipientThreadIds, $thread->id);
                    }
                    array_push($possibleCommonThreadIdsPerUser, $recipientThreadIds);
                }

                $possibleCommonThreadIds = $possibleCommonThreadIdsPerUser[0];
                foreach ($possibleCommonThreadIdsPerUser as $possibleCommonThreadIdsForUser){
                    $possibleCommonThreadIds = array_intersect($possibleCommonThreadIds, $possibleCommonThreadIdsForUser);
                }

                $recipientNumber = count($userArray);
                foreach ($possibleCommonThreadIds as $possibleCommonThreadId){
                    $thread = Thread::find($possibleCommonThreadId);
                    $threadParticipantNumber = count($thread->participants);
                    if($threadParticipantNumber == $recipientNumber){
                        return $thread;
                    }

                }

        }


        public function getThreadsForUser($user) {

                // Cmgmyr\Messenger (deprecated) documentation:
                //
                // All threads, ignore deleted/archived participants:
                //      $threads = Thread::getAllLatest();
                //
                // All threads that user is participating in:
                //      $threads = Thread::forUser($user->id)->latest('updated_at')->get();
                //
                // All threads that user is participating in, with new messages:
                //      $threads = Thread::forUserWithNewMessages($user->id);//->latest('updated_at')->get();

                $threads = Thread::forUser($user->id);

                return $threads;

        }


        public function getUnreadThreadsForUser($user) {

                $threads = Thread::forUserWithNewMessages($user->id);

                return $threads;

        }


        public function getThreadForUser($user, $threadId, $markAsRead = true) {

                $thread = Thread::find($threadId);

                if($thread == null){

                        throw new GameException("THREAD.NOT.FOUND");

                }

                $this->checkUserParticipantOfThread($user, $thread);

                if($markAsRead) {
                        $thread->markAsRead($user->id);
                }

                return $thread;

        }


        public function addUsersToThread($user, $userArray, $thread){

                $attributes = [
                    "thread.recipients" => sizeof($userArray),
                ];
                $this->validator->check($attributes, "USER.USERSNOTADDEDTOTHREAD");

                $this->checkUserParticipantOfThread($user, $thread);

                $userIds = array();
                foreach ($userArray as $user) {
                    array_push($userIds, $user->id);
                }
                $thread->addParticipants($userIds);

        }

    
}
