<?php namespace Game\Managers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

use Game\User;
use Game\Services\PolicyComplianceService;
use Game\Services\IdTokenService;
use Game\Exceptions\GameException;

/**
 * Description of MatchManager
 *
 * @author fvo
 */
class AccountManager {
    
        protected $passwords;

        protected $sessionLangToken = "language";
        protected $fallbackLocale = "en";
        protected $allowedLocales = ["en", "de"];
        
        
        public function __construct(PasswordBroker $passwords) {
                
                $this->passwords = $passwords;
                
        }
        
        
        public function changeLanguageForUser($user, $lang){

                if(in_array($lang, $this->allowedLocales)){
                    $user->language = $lang;
                    $user->save();
                }

        }
        
        
        public function changeLanguageForSession($lang) {
            
                if(in_array($lang, $this->allowedLocales)){
                    Session::set($this->sessionLangToken, $lang);
                    Log::info("Switched to $lang");
                }
                
        }


        public function getSessionAppLocale() {

                if(!Session::get($this->sessionLangToken)){
                    Session::set($this->sessionLangToken, $this->fallbackLocale);
                }
                
                return Session::get($this->sessionLangToken);
                
        }


        public function getUserAppLocale($user) {
            
                if($user && $user->language){
                    Session::set($this->sessionLangToken, $user->language);
                }
                
                return $this->getSessionAppLocale();
                
        }
        
        
        public function setNameForUser($user, $name) {
            
                $user->name = $name;
                $user->save();
            
        }
        
        
        public function setEmailForUser($user, $email) {
            
                $user->email = $email;
                $user->save();
            
        }

        
        public function setAvatarFileForUser($user, $avatarFile){

                if($avatarFile->isValid()){
                    
                    $oldAvatarFileName = $user->avatarfile;
                    $newUserAvatarFileName = $user->name . "_" . uniqid() . "." . $avatarFile->getClientOriginalExtension();
                    
                    $fileManager = new FileManager();
                    
                    $fileManager->saveAvatarFileAs($avatarFile, $newUserAvatarFileName);
                    $user->avatarfile = $newUserAvatarFileName;

                    $fileManager->deleteAvatarFile($oldAvatarFileName);
                
                    $user->save();
                    
                } else {
                    Log::info("Uploaded file is not valid");
                }

        }


        public function setPasswordForUser($user, $password) {
                
                $user->password = bcrypt($password);
                $user->save();
                
        }
        
        
        public function registerNewUserWith($username, $email, $password, $language) {
                
                return User::create([
                    'name' => $username,
                    'email' => $email,
                    'password' => bcrypt($password),
                    'avatarfile' => "default.png",
                    'language' => $language
		]);
	
        }
        
        
        public function sendPasswordResetLink($email) {

                $msg = null;
		$result = $this->passwords->sendResetLink(
                        ["email" => $email],
                        function($message) {
                            $msg = $message;
                            $message->subject(trans("passwords.resetemail.subject"));
                        }
                );
                
		if ($result == PasswordBroker::INVALID_USER) {
                        Session::flash("invalidfields", ["user_email"]);
                        throw new GameException("USER.NOT.FOUND.BY.EMAIL");
                }
                
                return PasswordBroker::RESET_LINK_SENT;
            
        }
        
        
        public function resetPassword($token, $email, $password, $passwordConfirmation) {
                
                $credentials = [
			'email' => $email,
                        'password' => $password,
                        'password_confirmation' => $passwordConfirmation,
                        'token' => $token
		];
                
                $result = $this->passwords->reset(
                        $credentials,
                        function($user, $password) {
                            $user->password = bcrypt($password);
                            $user->save();
                            Auth::login($user);
                        }
		);

		if ($result == PasswordBroker::PASSWORD_RESET) {
			return $result;
                }
                
                throw new GameException("PASSWORD.NOT.CHANGED", new MessageBag([trans($result)]));
        }
        
        
        public function setSocketJoinId($user) {
        
                $user->joinid = IdTokenService::createUUID($user->name);
                $user->save();
            
        }
    
}
