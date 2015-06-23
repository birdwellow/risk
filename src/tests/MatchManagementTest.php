<?php

use Game\User;
use Game\Model\Match;
use Game\Model\Invitation;
use Illuminate\Support\Facades\Session;

class MatchManagementTest extends TestCase {
    
    
        /*
         * The MatchManagment must fulfill the following tests:
         *      Overview:
         *      * testOverviewIsUnpopulatedByDefault                The overview page must be unpopulated for by default
         * 
         *      Match creation:
         *      * testUnjoinedUserCanCreateMatchAndAutomaticallyJoins
         *                                                          An unjoined user can create a match
         *      * testJoinedUserCannotCreateMatch                   An joined user cannot create a 
         *      - testMatchCannotBeCreatedWithWrongParameters       A match cannot be created when entering invalid parameters
         * 
         *      Match joining:
         *      - testUnjoinedUserCanJoinAWaitingMatch              An unjoined user can join a waiting match
         *      - testUserCannotJoinAStartedMatch                   A user cannot join a started match
         *      - testJoinedUserCannotJoinAnyMatch                  A joined user cannot join any match
         * 
         *      Match administration:
         *      * testOnlyCreatorCanAdministrateMatch               A match can only be administrated by its creator
         *      * testOnlyCreatorCanStartMatch                      A match can only be started by its creator
         *      - testMatchCannotBeAdministratedWithWrongParameters A match cannot be adminstrated with invalid parameters
         */
    
    
        public function setUp() {
                
                parent::setUp();
            
                $this->Subberbazi = User::find(1);
                $this->Oberbazi = User::find(2);
                $this->SoEinBazi = User::find(3);
                $this->ChAoT = User::find(4);
                
        }
        
 
        
        /**
	 * Test: New User calls the index page
         * Expectations:
         *      * No matches joins displayed
         *      * No match invitations displayed
	 *
	 * @return void
	 */
	public function testOverviewIsUnpopulatedByDefault()
	{
                $this->be($this->Oberbazi);
                
                $response = $this->call('GET', '/');

		$this->assertResponseOk();
                $this->assertContains("Du bist keinem Match beigetreten.", $response->getContent());
                $this->assertContains("Keine Spiel-Einladungen :(", $response->getContent());
	}

	/**
	 * Test: A user, that has not joined any match can create a new match.
         *       Subsequently he is redirected to join the new match.
         * Expectations:
         *      * The user is redirected to the join match page for the match he has just created
         *      * Another user sees a match invitation on his overview page.
	 *
	 * @return void
	 */
	public function testUnjoinedUserCanCreateMatch()
	{
                $this->createMatch($this->Oberbazi, [
                    "name" => "Mein Subber-Match",
                    "invited_players" => "Subberbazi, SoEinBazi, ChAoT, ",
                    "message" => "Auf gehts Bazis!"
                ]);
                
                $this->be($this->Subberbazi);
                $subberbaziOverviewResponse = $this->call('GET', '/');
                
                $this->assertResponseOk();
                $this->assertContains("Auf gehts Bazis!", $subberbaziOverviewResponse->getContent());
                $this->assertContains("Join match &#039;Mein Subber-Match&#039;", $subberbaziOverviewResponse->getContent());
        }

        
	/**
	 * Test: A user can be invited to many matches, but can only join one.
         *       When joining a match, other invitations are rejected automatically.
	 *
	 * @return void
	 */
        public function testJoiningAMatchRejectsOtherInvitations() {
            
                /*
                 * Create first test match
                 */
            
                $this->createMatch($this->Oberbazi, [
                    "name" => "Oberbazis-Match",
                    "invited_players" => "SoEinBazi, ChAoT",
                    "message" => "Mein_Match_(Oberbazi)!"
                ]);
            
                /*
                 * Create second test match
                 */
            
                $this->createMatch($this->Subberbazi, [
                    "name" => "Subberbazis-Match",
                    "invited_players" => "SoEinBazi, ChAoT, ",
                    "message" => "Mein_Match_(Subberbazi)!",
                ]);
                
                
                
                /*
                 * Join one of the created matches as invited third user
                 */
                
                $this->be($this->SoEinBazi);
                $soEinBaziOverviewResponse = $this->call('GET', '/');
                
                $this->assertResponseOk();
                $this->assertContains("Mein_Match_(Oberbazi)", $soEinBaziOverviewResponse->getContent());
                $this->assertContains("match/join/1", $soEinBaziOverviewResponse->getContent());
                $this->assertContains("Mein_Match_(Subberbazi)", $soEinBaziOverviewResponse->getContent());
                $this->assertContains("match/join/2", $soEinBaziOverviewResponse->getContent());
                
                $this->joinMatch($this->SoEinBazi, 2);
                
                $soEinBaziOverviewResponse2 = $this->call('GET', '/');
                $this->assertResponseOk();
                $this->assertNotContains("Mein_Match_(Oberbazi)", $soEinBaziOverviewResponse2->getContent());
                $this->assertNotContains("match/join/1", $soEinBaziOverviewResponse2->getContent());
                $this->assertNotContains("Mein_Match_(Subberbazi)", $soEinBaziOverviewResponse2->getContent());
                $this->assertNotContains("match/join/2", $soEinBaziOverviewResponse2->getContent());
                
                
                $this->be($this->Subberbazi);
                $subberbaziOveriewResponse = $this->call("GET", "/");
                $this->assertContains("SoEinBazi", $subberbaziOveriewResponse->getContent());
                
                $this->be($this->Oberbazi);
                $oberbaziOveriewResponse = $this->call("GET", "/");
                $this->assertContains("Invitations were rejected", $oberbaziOveriewResponse->getContent());
                $this->assertContains("Von SoEinBazi", $oberbaziOveriewResponse->getContent());
                
                
        }

        
	/**
	 * Test: A user, who already joined a match, can not be invited to a new match.
	 *
	 * @return void
	 */
        public function testJoinedUserCannotBeInvited() {
            
                /*
                 * Create first test match
                 */
            
                $this->createMatch($this->Oberbazi, [
                    "name" => "Oberbazis-Match",
                    "invited_players" => "SoEinBazi, ChAoT",
                    "message" => "Mein_Match_(Oberbazi)!",
                ]);
                
                
                /*
                 * Join the created match as invited user
                 */
                
                $this->be($this->SoEinBazi);
                $soEinBaziOverviewResponse = $this->call('GET', '/');
                
                $this->assertResponseOk();
                $this->assertContains("Mein_Match_(Oberbazi)", $soEinBaziOverviewResponse->getContent());
                $this->assertContains("match/join/1", $soEinBaziOverviewResponse->getContent());
                
                
                
            
            
                /*
                 * Create second test match
                 */
                
                $this->createMatch($this->Subberbazi, [
                    "name" => "Subberbazis-Match",
                    "invited_players" => "SoEinBazi, ChAoT, ",
                    "message" => "Mein_Match_(Subberbazi)!"
                ]);
                
                
                $this->be($this->SoEinBazi);
                $soEinBaziOverviewResponse2 = $this->call('GET', '/');
                
                $this->assertResponseOk();
                $this->assertNotContains("Mein_Match_(Subberbazi)", $soEinBaziOverviewResponse2->getContent());
                $this->assertNotContains("match/join/2", $soEinBaziOverviewResponse2->getContent());
            
            
            
        }

        
	/**
	 * Test: A user, who already joined a match, can not create a new match.
	 *
	 * @return void
	 */
        public function testJoinedUserCannotCreateNewMatch() {
            
            
            
        }

        
	/**
	 * Test: A user, who created a match, can
         *       * invite users after the match was created
         *       * administrate the match
         *       * cancel the match
	 *
	 * @return void
	 */
        public function testMatchCreatorCanAdministrateMatch() {
            
            
            
        }

        
	/**
	 * Test: A user, who created a match, can not
         *       * invite users after the match was created
         *       * administrate the match
         *       * cancel the match
	 *
	 * @return void
	 */
        public function testNonMatchCreatorCannotAdministrateMatch() {
            
            
            
        }
        
        
        /*
         * 
         * Private Helper methods
         * 
         */
        
        private function createMatch($asUser, $withParameters = array()){
            
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
        
        
        private function joinMatch($asUser, $matchId) {
            
                $this->be($asUser);
            
                $this->call('GET', '/match/join/'.$matchId);
                $this->assertResponseOk();
                
                $this->call('POST', '/match/join/'.$matchId, [
                    "_token" => Session::token()
                ]);
                $this->assertRedirectedToRoute('match.goto');
        }

}
