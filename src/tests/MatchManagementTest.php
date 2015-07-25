<?php

use Game\User;
use Game\Model\Match;
use Illuminate\Support\Facades\Session;



class MatchManagementTest extends TestCase {
    
        
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
         *  Complex: "Overview"
         * 
         **********************/
        
        /**
	 * Test: New User calls the index page
         * Expectations:
         *      * No matches joins displayed
         *      * No (new) messages displayed
	 *
	 * @return void
	 */
	public function testOverviewIsUnpopulatedByDefault(){
                $this->be($this->Oberbazi);
                
                $response = $this->call('GET', '/');

		$this->assertResponseOk();
                $this->assertContains("Du bist keinem Match beigetreten.", $response->getContent());
                $this->assertContains("Keine Spiel-Einladungen :(", $response->getContent());
	}
        
        
        
        
 
        /**********************
         * 
         *  Complex "Match creation"
         * 
         **********************/
        
        

	/**
	 * Test: A user, that is not currently joined any match, can create a new match.
         *       He is automatically joined to his new match.
         * Expectations:
         *      * The user is redirected to his overview page, where he sees his match running
	 *
	 * @return void
	 */
        
	public function testUnjoinedUserCanCreateMatchAndAutomaticallyJoins(){
                /*$this->createMatch($this->Oberbazi, [
                    "name" => "Mein Subber-Match",
                    "invited_players" => "Subberbazi, SoEinBazi, ChAoT, ",
                    "message" => "Auf gehts Bazis!"
                ]);
                
                $this->be($this->Subberbazi);
                $subberbaziOverviewResponse = $this->call('GET', '/');
                
                $this->assertResponseOk();
                $this->assertContains("Auf gehts Bazis!", $subberbaziOverviewResponse->getContent());
                $this->assertContains("Join match &#039;Mein Subber-Match&#039;", $subberbaziOverviewResponse->getContent());*/
        }
        
        
        
        /**
	 * Test: A user, that is currently joined a match, cannot join another match
         * Expectations:
         *      * The user is redirected back with a corresponding error message
	 *
	 * @return void
	 */
        public function testJoinedUserCannotCreateMatch(){}
        
        
        
        /**
	 * Test: When entering invalid parameters, a match cannot be created
         * Expectations:
         *      * The creator is redirected back to the create match form with
         *        corresponding error message(s)
	 *
	 * @return void
	 */
        public function testMatchCannotBeCreatedWithWrongParameters(){}
        
        
        
        
        /**********************
         * 
         *  Complex "Match joining":
         * 
         **********************/
        
        
        
        /**
	 * Test: A user, that is not currently joined to a match, can join a match
         *      in state "waiting"
         * Expectations:
         *      * The user is redirected back with a corresponding error message
	 *
	 * @return void
	 */
        public function testUnjoinedUserCanJoinAWaitingMatch(){}
        
        
        
        /**
	 * Test: A user, that is currently joined a match, cannot join another match
         * Expectations:
         *      * The user is redirected back with a corresponding error message
	 *
	 * @return void
	 */
        public function testJoinedUserCannotJoinAnyMatch(){}
        
        public function testUserCannotJoinAStartedMatch(){}
        
        public function testUserCannotJoinACancelledMatch(){}
        
        
        
        
        /**********************
         * 
         *  Complex "Match administration":
         * 
         **********************/
        
        
         public function testOnlyCreatorCanAdministrateMatch(){}
         
         public function testOnlyCreatorCanStartMatch(){}
         
         public function testOnlyCreatorCanCancelMatch(){}
         
         public function testMatchCannotBeAdministratedWithWrongParameters(){}
        
        
        
        
        /**********************
         * 
         *  Complex "Match inviting":
         * 
         **********************/
        
        
         public function testUserCanBeInvited(){}
         
         public function testJoinedUserCannotBeInvited(){}
                 
                 
                 
                 
        
        /*
         * 
         * Private Helper methods
         * 
         */
        
        private function createStandardTestMatch($asUser, $withParameters = array()){
            
                $this->be($asUser);
                $createMatchResponse = $this->call('GET', '/match/new');
                        $this->assertResponseOk();
                        $this->assertContains('<option value="Earth">', $createMatchResponse->getContent());
                        $this->assertContains('<option value="6" selected="">6</option>', $createMatchResponse->getContent());
                
                if(!isset($withParameters["name"])){
                    $withParameters["name"] = "Default_Name";
                }
                if(!isset($withParameters["closed"])){
                    $withParameters["closed"] = true;
                }
                if(!isset($withParameters["maxusers"])){
                    $withParameters["maxusers"] = 6;
                }
                if(!isset($withParameters["mapName"])){
                    $withParameters["mapName"] = "Earth";
                }
                if(!isset($withParameters["invited_players"])){
                    $withParameters["invited_players"] = "";
                }
                if(!isset($withParameters["message"])){
                    $withParameters["message"] = "Default_Message";
                }
                $withParameters["_token"] = Session::token();
                
                $this->call('POST', '/match/create', $withParameters);
                
                $newMatch = Match::all()->last();
                $this->assertRedirectedToRoute('match.join.init', ['id' => $newMatch->id]);
            
        }

}
