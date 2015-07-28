<?php

use Game\User;
use Cmgmyr\Messenger\Models\Thread;
use Illuminate\Support\Facades\Log;



class MessageManagementTest extends TestCase {
    
        
        protected $Subberbazi, $Oberbazi, $SoEinBazi, $ChAoT;
    
    
        public function setUp() {
                
                parent::setUp();
            
                $this->Subberbazi = User::find(1);
                $this->Oberbazi = User::find(2);
                $this->SoEinBazi = User::find(3);
                $this->ChAoT = User::find(4);
                
        }
        
        
 
        /**********************
         * 
         *  Complex: "Standard Messaging Operations"
         * 
         **********************/
        
        
        
        /**
	 * Test: User creates a new thread with the first message
         * Expectations:
         *      * A new thread is created
         *      * All selected participients can see the message, too
         *      * Other users cannot see the message
	 *
	 * @return void
	 */
	public function testUserCanCreateNewThreadWithValidInputs(){
            
                $this->createStandardTestThread($this->Subberbazi);

                $subberbaziThread1Response = $this->call("GET", "thread/1");
                $this->assertContains("Test_Subject", $subberbaziThread1Response->getContent());
                $this->assertContains('onclick="loadThread(1, this)"', $subberbaziThread1Response->getContent());

                $chaotAllThreadsResponse = $this->call("GET", "threads");
                $this->assertResponseOk();
                $this->assertContains("Test_Subject", $chaotAllThreadsResponse->getContent());
                $this->assertContains('onclick="loadThread(1, this)"', $chaotAllThreadsResponse->getContent());

                $this->be($this->SoEinBazi);
                $soeinbaziAllThreadsResponse = $this->call("GET", "threads");

                $this->assertResponseOk();
                $this->assertNotContains("Test_Subject", $soeinbaziAllThreadsResponse->getContent());
                $this->assertNotContains('onclick="loadThread(1,"', $soeinbaziAllThreadsResponse->getContent());
                $this->assertNotContains('class="newmessagescount">', $soeinbaziAllThreadsResponse->getContent());
            
        }
        
        
        
        /**
	 * Test: User creates a new thread with the first message
         * Expectations:
         *      * A new thread is created
         *      * All selected participients can see the message, too
         *      * Other users cannot see the message
	 *
	 * @return void
	 */
	public function testUserSeesNewMessagesInHisOverview(){
            
                $this->be($this->ChAoT);
                $chaotOverViewResponseEmpty = $this->call("GET", "/");
                $this->assertContains("Keine neuen Nachrichten", $chaotOverViewResponseEmpty->getContent());
                
                
                $this->createStandardTestThread($this->Subberbazi);


                $this->be($this->ChAoT);
                $chaotOverViewResponseOneNewMessage = $this->call("GET", "/");
                $this->assertContains("Neue Nachricht in einem Thread", $chaotOverViewResponseOneNewMessage->getContent());
                
                
                $this->createStandardTestThread($this->Oberbazi);
                
                
                $this->be($this->ChAoT);
                $chaotOverViewResponseTwoNewMessages = $this->call("GET", "/");
                $this->assertContains("Neue Nachrichten in 2 Threads", $chaotOverViewResponseTwoNewMessages->getContent());
            
        }
        
        
        
        /**
	 * Test: User posts a new message in a thread 
         * Expectations:
         *      * The new message is visible in the thread for all participants
	 *
	 * @return void
	 */
        public function testUserCanSendMessageInThread(){
            
                $this->createStandardTestThread($this->Subberbazi);
                
                $this->be($this->Oberbazi);
                
                $startResponse = $this->call("GET", "/thread/1");
                $this->assertResponseOk();
                $this->assertNotContains("OberbazisMessage", $startResponse->getContent());
                
                $this->call("POST", "/thread/1/newmessage", [
                    "message" => "OberbazisMessage",
                    "_token" => csrf_token(),
                ]);
                $this->assertRedirectedTo("thread/1");
                $endResponse = $this->call("GET", "thread/1");
                $this->assertContains("OberbazisMessage", $endResponse->getContent());
                
        }
        
        
        
        /**
	 * Test: User adds users to a thread 
         * Expectations:
         *      * The selected users are added to the threat as participants
         *      * The added users see the thread as unread
	 *
	 * @return void
	 */
        public function testUserCanAddUsersToThread(){
            
                $this->createStandardTestThread($this->Subberbazi, [
                    "usernames" => "Oberbazi"
                ]);
                
                $this->be($this->Oberbazi);
                $startResponse = $this->call("GET", "/thread/1");
                $this->assertResponseOk();
                $this->assertNotContains("SoEinBazi", $startResponse->getContent());
                $this->assertNotContains("ChAoT", $startResponse->getContent());
                
                $this->call("POST", "/thread/1/addusers", [
                    "usernames" => "SoEinBazi,ChAoT",
                    "_token" => csrf_token(),
                ]);
                $this->assertRedirectedTo("thread/1");
                $endResponse = $this->call("GET", "thread/1");
                $this->assertContains("SoEinBazi", $endResponse->getContent());
                $this->assertContains("ChAoT", $endResponse->getContent());
                
                $this->be($this->ChAoT);
                $chaotResponse = $this->call("GET", "/threads");
                $this->assertResponseOk();
                $this->assertContains("Test_Subject", $chaotResponse->getContent());
                
        }
        
        
 
        /**********************
         * 
         *  Complex: "Validation"
         * 
         **********************/
        
        
        
        /**
	 * Test: User cannot create a new thread with invalid inputs
         * Expectations:
         *      * Subject must not be empty and not longer than 20 characters
         *      * At least one recipient must be selected
	 *
	 * @return void
	 */
        public function testUserCanotCreateNewThreadWithInvalidInputs(){
                
                $this->createStandardTestThread($this->Subberbazi, [
                    "subject" => "",
                    "usernames" => "",
                    "message" => "",
                    "_token" => csrf_token()
                ]);
                $this->assertRedirectedTo("thread/new");
                $errorResponse1 = $this->call("GET", "thread/new");
                $this->assertContains("Please enter a subject.", $errorResponse1->getContent());
                
                $this->createStandardTestThread($this->Subberbazi, [
                    "subject" => "This is a test subject",
                    "usernames" => "",
                    "message" => "",
                    "_token" => csrf_token()
                ]);
                $this->assertRedirectedTo("thread/new");
                $errorResponse2 = $this->call("GET", "thread/new");
                $this->assertContains("You did not select any users.", $errorResponse2->getContent());
                $this->assertContains("This is a test subject", $errorResponse2->getContent());
                
                $this->createStandardTestThread($this->Subberbazi, [
                    "subject" => "This is a test subject",
                    "usernames" => "Oberbazi,ChAoT",
                    "message" => "",
                    "_token" => csrf_token()
                ]);
                $this->assertRedirectedTo("thread/1");

        }
        
        
        
        /**
	 * Test: User cannot send a message in a thread with invalid inputs
         * Expectations:
         *      * The text field must not be empty
	 *
	 * @return void
	 */
        public function testUserCanotSendNewMessageInThreadWithInvalidInputs(){
            
                $this->createStandardTestThread($this->Subberbazi);
                
                $this->call("GET", "/thread/1");
                $this->assertResponseOk();

                $this->call("POST", "thread/1/newmessage", [
                    "message" => "",
                    "_token" => csrf_token()
                ]);
                $this->assertRedirectedTo("thread/1");
                
                $subberbaziErrorResponse = $this->call("GET", "/threads");
                $this->assertContains("Please enter a message.", $subberbaziErrorResponse->getContent());
                
        }
        
        
        
        /**
	 * Test: User cannot add users to a thread with invalid inputs
         * Expectations:
         *      * At least one existing user must be selected
	 *
	 * @return void
	 */
        public function testUserCanotAddUsersToThreadWithInvalidInputs(){
            
                $this->createStandardTestThread($this->Subberbazi);
                
                $this->call("GET", "/thread/1");
                $this->assertResponseOk();

                $this->call("POST", "thread/1/addusers", [
                    "usernames" => "",
                    "_token" => csrf_token()
                ]);
                $this->assertRedirectedTo("thread/1");
                
                $subberbaziErrorResponse = $this->call("GET", "/threads");
                $this->assertContains("You did not select any users.", $subberbaziErrorResponse->getContent());
                
        }
        
        
        
        /**
	 * Test: User cannot display a non existing thread
         * Expectations:
         *      * An error message is displayed
	 *
	 * @return void
	 */
        public function testUserCanotViewNonExistingThread(){
            
                $this->be($this->Subberbazi);
                
                $this->call("GET", "/threads");
                $this->assertResponseOk();

                $this->call("GET", "thread/417");
                $this->assertRedirectedTo("threads");
                
                $subberbaziErrorResponse = $this->call("GET", "/threads");
                $this->assertContains("Could not find thread.", $subberbaziErrorResponse->getContent());
                
        }
        
        
        
        /**
	 * Test: User cannot send a message in a non existing thread
         * Expectations:
         *      * An error message is displayed
	 *
	 * @return void
	 */
        public function testUserCanotSendMessageInNonExistingThread(){
            
                $this->be($this->Subberbazi);
                
                $this->call("GET", "/threads");
                $this->assertResponseOk();

                $this->call("POST", "thread/1/newmessage", [
                    "message" => "Hey there",
                    "_token" => csrf_token()
                ]);
                $this->assertRedirectedTo("threads");
                
                $subberbaziErrorResponse = $this->call("GET", "/threads");
                $this->assertContains("Could not find thread.", $subberbaziErrorResponse->getContent());
                
        }
        
        
        
        /**
	 * Test: User cannot add users to a non existing thread
         * Expectations:
         *      * An error message is displayed
	 *
	 * @return void
	 */
        public function testUserCanotAddUsersInNonExistingThread(){
            
                $this->be($this->Subberbazi);
                
                $this->call("GET", "/threads");
                $this->assertResponseOk();

                $this->call("POST", "thread/1/addusers", [
                    "usernames" => "Oberbazi,ChAoT",
                    "_token" => csrf_token()
                ]);
                $this->assertRedirectedTo("threads");
                
                $subberbaziErrorResponse = $this->call("GET", "/threads");
                $this->assertContains("Could not find thread.", $subberbaziErrorResponse->getContent());
                
        }
        
        
 
        /**********************
         * 
         *  Complex: "Thread privacy"
         * 
         **********************/
        
        
        
        /**
	 * Test: A user cannot see a thread or its messages, where he is not participant
         * Expectations:
         *      * After trying to display it, an error message is displayed
	 *
	 * @return void
	 */
	public function testNonParticipantCannotSeeThread(){
            
                $this->createStandardTestThread($this->Subberbazi, [
                    "usernames" => "Oberbazi,ChAoT"
                ]);
                
                $this->be($this->SoEinBazi);
                
                $this->call("GET", "/threads");
                $this->assertResponseOk();
                
                $this->call("GET", "/thread/1");
                $this->assertRedirectedTo("threads");
                
                $errorResponse = $this->call("GET", "threads");
                $this->assertContains("Du bist nicht Teilnehmer des Threads", $errorResponse->getContent());
                
        }
        
        
        
        /**
	 * Test: A user cannot send a message in a thread, where he is not participant
         * Expectations:
         *      * After sending the message, an error message is displayed
	 *
	 * @return void
	 */
	public function testNonParticipantCannotPostMessageInThread(){
            
                $this->createStandardTestThread($this->Subberbazi, [
                    "usernames" => "Oberbazi"
                ]);
                
                $this->be($this->SoEinBazi);
                
                $this->call("GET", "/threads");
                $this->assertResponseOk();
                
                $this->call("POST", "/thread/1/newmessage", [
                    "message" => "Not_allowed_message",
                    "_token" => Session::token()
                ]);
                $this->assertRedirectedTo("threads");
                
                $errorResponse = $this->call("GET", "threads");
                $this->assertContains("Du bist nicht Teilnehmer des Threads", $errorResponse->getContent());
                
                
                $this->be($this->Oberbazi);
                
                $oberbaziResponse = $this->call("GET", "/thread/1");
                $this->assertContains("This_is_a_test_message", $oberbaziResponse->getContent());
                $this->assertNotContains("Not_allowed_message", $oberbaziResponse->getContent());
                
        }
        
        
        
        /**
	 * Test: A user cannot send add users to a thread, where he is not participant
         * Expectations:
         *      * After trying to add users, an error message is displayed
	 *
	 * @return void
	 */
	public function testNonParticipantCannotAddUsersToThread(){
            
                $this->createStandardTestThread($this->Subberbazi, [
                    "usernames" => "Oberbazi"
                ]);
                
                $this->be($this->SoEinBazi);
                
                $this->call("GET", "/threads");
                $this->assertResponseOk();
                
                $response = $this->call("POST", "/thread/1/addusers", [
                    "usernames" => "ChAoT,SoEinBazi",
                    "_token" => Session::token()
                ]);
                $this->assertRedirectedTo("threads");
                
                $errorResponse = $this->call("GET", "threads");
                $this->assertContains("Du bist nicht Teilnehmer des Threads", $errorResponse->getContent());
                
                
                $this->be($this->Oberbazi);
                
                $oberbaziResponse = $this->call("GET", "/thread/1");
                $this->assertContains("Subberbazi", $oberbaziResponse->getContent());
                $this->assertNotContains("SoEinBazi", $oberbaziResponse->getContent());
                $this->assertNotContains("ChAoT", $oberbaziResponse->getContent());
                
        }
        
        
        
        
        
        private function createStandardTestThread($asUser, $withParameters = array()){
            
                $this->be($asUser);
                
            
                $this->call("GET", "/thread/new");
                $this->assertResponseOk();
                
                if(!isset($withParameters["subject"])){
                    $withParameters["subject"] = "Test_Subject";
                }
                if(!isset($withParameters["usernames"])){
                    $withParameters["usernames"] = "Oberbazi,ChAoT";
                }
                if(!isset($withParameters["message"])){
                    $withParameters["message"] = "This_is_a_test_message";
                }
                if(!isset($withParameters["reusethread"])){
                    $withParameters["reusethread"] = null;
                }
                $withParameters["_token"] = csrf_token();
                
                $this->call("POST", "thread/new", $withParameters);
                
                $thread = Thread::all()->last();
                
                return $thread;
                
                
        }

}
