<?php

use Game\User;
use Illuminate\Support\Facades\Session;
use Game\Model\Map;

class MatchManagerTest extends TestCase {
    
    
        protected $users = array();
    
    
        public function setUp() {
                
                parent::setUp();
            
                $user1 = new User([
                    "name" => "TestUser1",
                    "email" => "test1@test.de",
                    "language" => "de",
                    "colorscheme" => "classic",
                ]);
                $user1->save();
                $this->users["user1"] = $user1;
                
                $user2 = new User([
                    "name" => "TestUser2",
                    "email" => "test2@test.de",
                    "language" => "en",
                    "colorscheme" => "classic",
                ]);
                $user2->save();
                $this->users["user2"] = $user2;
                
        }
        
        public function tearDown() {
            
            foreach ($this->users as $userKey => $user) {
                
                $user->delete();
                
            }
            
            parent::tearDown();
            
        }

	/**
	 * Test: New User calls the index page
         * Expections:
         *      * No match invitations
         *      * No matches joined
	 *
	 * @return void
	 */
	public function testOverviewIsEmptyForNewUser()
	{
                $this->be($this->users["user1"]);
                
                $response = $this->call('GET', '/');

		$this->assertEquals(200, $response->getStatusCode());
                $this->assertContains("Du bist keinem Match beigetreten.", $response->getContent());
                $this->assertContains("Keine Spiel-Einladungen :(", $response->getContent());
	}

	/**
	 * Test: User calls a non existing match
         * Expections:
         *      * Match not found
	 *
	 * @return void
	 */
	public function testNonExistingMatchCallReturnsErrorFeedback()
	{
            
                $this->be($this->users["user1"]);
                
                $response = $this->call('GET', '/match/1000000000');
                
		$this->assertEquals(302, $response->getStatusCode());
                $this->assertSessionHas("message");
                
                $message = Session::get("message");
                $this->assertEquals($message->messageKey, "error.system.match.not.found");
            
        }

}
