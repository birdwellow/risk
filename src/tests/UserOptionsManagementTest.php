<?php

use Game\User;


        /*
         * The UserOptionsManagement must fulfill the following tests:
         *      Complex "Language":
         *      - testUserCanSwitchLanguagePersistently         A user can switch the language any time, is redirected
         *                                                      back and the language is saved
         * 
         *      Complex "User Data":
         *      - testUserCanSetOnlyValidUsernames              A user can configure only valid user names
         *      - testUserCanSetUsernamesOnlyWhenNotJoined      A user can only change his user name when he is not joined to a match
         *      - testUserCanSetOnlyValidEmailAddress           A user can configure only a valid email address
         *      - testUserCanUseOnlyValidAvatarFiles            A user can upload only valid avatar files
         * 
         *      Complex "Password":
         *      - testUserCanSetOnlyValidPasswords              A user can set only valid passwords
         * 
         */

class UserOptionsManagementTest extends TestCase {
    
    
        public function setUp() {
                
                parent::setUp();
            
                $this->Subberbazi = User::find(1);
                
        }
        
 
        /**********************
         * 
         *  Complex: "Language"
         * 
         **********************/
        
        /**
	 * Test: 
         * Expectations:
         *      * 
	 *
	 * @return void
	 */
        
        public function testUserCanSwitchLanguagePersistently(){}
        
 
        /**********************
         * 
         *  Complex: "User Data"
         * 
         **********************/
        
        /**
	 * Test: 
         * Expectations:
         *      * 
	 *
	 * @return void
	 */
        
	public function testUserCanSetOnlyValidUsernames(){}
        
        
        /**
	 * Test: 
         * Expectations:
         *      * 
	 *
	 * @return void
	 */
        
        public function testUserCanSetUsernamesOnlyWhenNotJoined(){}
        
        
        /**
	 * Test: 
         * Expectations:
         *      * 
	 *
	 * @return void
	 */
        
        public function testUserCanSetOnlyValidEmailAddress(){}
        
        
        /**
	 * Test: 
         * Expectations:
         *      * 
	 *
	 * @return void
	 */
        
        public function testUserCanUseOnlyValidAvatarFiles(){}
        
 
        /**********************
         * 
         *  Complex: "Password"
         * 
         **********************/
        
        /**
	 * Test: 
         * Expectations:
         *      * 
	 *
	 * @return void
	 */
        
        public function testUserCanSetOnlyValidPasswords(){}

}
