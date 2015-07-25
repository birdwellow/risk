<?php

use Game\User;



class UserOptionsManagementTest extends TestCase {
    
        
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
