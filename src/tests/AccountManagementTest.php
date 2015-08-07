<?php

use Illuminate\Support\Facades\Log;

use Game\User;



class AccountManagementTest extends TestCase {
    
        
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
	 * Test: User switches language
         * Expectations:
         *      * The language is stored for the user
         *      * When logged in, all subsequent requests are rendered with the selected language
	 *
	 * @return void
	 */
        
        public function testUserCanSwitchLanguagePersistently(){
            
            $this->assertEquals("en", User::find(1)->language);
            
            $this->be($this->Subberbazi);
            
            $optionsResponse1 = $this->call("GET", "/options");
            $this->assertResponseOk();
            $this->assertContains("User Options", $optionsResponse1->getContent());
            
            $this->call("GET", "switchlanguage/de");
            $this->assertRedirectedTo("options");
            
            $optionsResponse2 = $this->call("GET", "/options");
            $this->assertResponseOk();
            $this->assertContains("User-Optionen", $optionsResponse2->getContent());
            
            $this->call("GET", "/auth/logout");
            
            $this->be($this->Subberbazi);
            
            $optionsResponse3 = $this->call("GET", "/options");
            $this->assertResponseOk();
            $this->assertContains("User-Optionen", $optionsResponse3->getContent());
            
            $this->assertEquals("de", User::find(1)->language);
            
        }
        
        /**
	 * Test: User tries to switch to a non-supported language
         * Expectations:
         *      * Nothing changes
	 *
	 * @return void
	 */
        
        public function testUserCannotSwitchToNonSupportedLanguage(){
            
            $this->assertEquals("en", User::find(1)->language);
            
            $this->be($this->Subberbazi);
            
            $optionsResponse1 = $this->call("GET", "/options");
            $this->assertResponseOk();
            $this->assertContains("User Options", $optionsResponse1->getContent());
            
            $this->call("GET", "switchlanguage/random");
            $this->assertRedirectedTo("options");
            
            $optionsResponse2 = $this->call("GET", "/options");
            $this->assertResponseOk();
            $this->assertContains("User Options", $optionsResponse2->getContent());
            
            $this->assertEquals("en", User::find(1)->language);
            
        }
        
 
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
        
	public function testUserCanSetOnlyValidUsernames(){
            
            $this->be($this->Subberbazi);
            
            $this->call("GET", "/options");
            $response = $this->call("POST", "/options", [
                "new_user_name" => "",
                "new_user_email" => "subber@bazi.de",
                "_token" => csrf_token()
            ]);
            $this->assertRedirectedTo("options");
            $userNameRequiredResponse = $this->call("GET", "/options");
            $this->assertContains("The User Name field is required.", $userNameRequiredResponse->getContent());
            
            
            $this->call("GET", "/options");
            $response = $this->call("POST", "/options", [
                "new_user_name" => "Sub",
                "new_user_email" => "subber@bazi.de",
                "_token" => csrf_token()
            ]);
            $this->assertRedirectedTo("options");
            $userNameRequiredResponse = $this->call("GET", "/options");
            $this->assertContains("The User Name must be at least 5 characters", $userNameRequiredResponse->getContent());
            
            
            $this->call("GET", "/options");
            $response = $this->call("POST", "/options", [
                "new_user_name" => "Sub",
                "new_user_email" => "subber@bazi.de",
                "_token" => csrf_token()
            ]);
            $this->assertRedirectedTo("options");
            $userNameRequiredResponse = $this->call("GET", "/options");
            $this->assertContains("The User Name must be at least 5 characters", $userNameRequiredResponse->getContent());
            
        }
        
        
        /**
	 * Test: 
         * Expectations:
         *      * 
	 *
	 * @return void
	 */
        
        public function testUserCanSetUsernamesOnlyWhenNotJoinedAMatch(){}
        
        
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
        
        
        /**
	 * Test: 
         * Expectations:
         *      * 
	 *
	 * @return void
	 */
        
        public function testNothingIsSavedWhenNewNameOrNewEmailIsWrong(){}
        
        
        /**
	 * Test: 
         * Expectations:
         *      * 
	 *
	 * @return void
	 */
        
        public function testInvalidAvatarFileDoesNotAffectNewNameOrNewEmailStorage(){}
        
 
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
        
        public function testUserCanSetValidPasswords(){}
        
        
        /**
	 * Test: 
         * Expectations:
         *      * 
	 *
	 * @return void
	 */
        
        public function testUserCannotSetPasswordWhenOldPasswordIsWrong(){}
        
        
        /**
	 * Test: 
         * Expectations:
         *      * 
	 *
	 * @return void
	 */
        
        public function testUserCannotSetPasswordWhenNewPasswordIsInvalid(){}
        
        
        /**
	 * Test: 
         * Expectations:
         *      * 
	 *
	 * @return void
	 */
        
        public function testUserCannotSetPasswordWhenPasswordAndConfirmationDiffer(){}

}
