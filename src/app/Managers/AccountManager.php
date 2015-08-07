<?php namespace Game\Managers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

use Game\User;
use Game\Services\PolicyComplianceService;
use Game\Exceptions\GameException;

/**
 * Description of MatchManager
 *
 * @author fvo
 */
class AccountManager {
    
        protected $validator;
        protected $passwords;

        protected $sessionLangToken = "language";
        protected $fallbackLocale = "en";
        protected $allowedLocales = ["en", "de"];
        
        
        public function __construct(PolicyComplianceService $validator, PasswordBroker $passwords) {
                
                $this->validator = $validator;
                $this->passwords = $passwords;
                
        }
        
        
        protected function checkUserOptionsForUser($user, $userName, $userEmail){
            
                $attributes = array();
                if($user->name !== $userName){
                    $attributes["new_user_name"] = $userName;
                }
                if($user->email !== $userEmail){
                    $attributes["new_user_email"] = $userEmail;
                }
                
                $this->validator->check($attributes, "INVALID.OPTIONS");
            
        }
        
        
        protected function checkPasswordData($email, $password, $newPassword, $newPasswordConfirm){
                
                $attributes = [
                    "user_password" => [
                        $password,
                        "auth:" . $email
                    ],
                    "new_user_password" => $newPassword,
                    "new_user_password_confirmation" => $newPasswordConfirm,
                ];
                
                $this->validator->check($attributes, "PASSWORD.NOT.CHANGED");
            
        }
        
        
        public function changeLanguageForUser($user, $lang){

                if(in_array($lang, $this->allowedLocales)){
                    $user->language = $lang;
                    $user->save();
                }

        }
        
        
        public function changeLanguageForSession($lang) {
            
                if(in_array($lang, $this->allowedLocales)){
                    Session::set($this->sessionLangToken, $this->fallbackLocale);
                }
                
        }


        public function getUserAppLocale($user) {
            
                if($user && $user->language){
                    Session::set($this->sessionLangToken, $user->language);
                }

                if(!Session::get($this->sessionLangToken)){
                    Session::set($this->sessionLangToken, $this->fallbackLocale);
                }
                
                return Session::get($this->sessionLangToken);
                
        }

        
        public function changeOptionsForUser($user, $options){

                $newUserName = $options['new_user_name'];
                $newUserEmail = $options['new_user_email'];
                
                $this->checkUserOptionsForUser($user, $newUserName, $newUserEmail);
                
                $user->name = $newUserName;
                $user->email = $newUserEmail;

                $newUserAvatarFile = ( isset($options['new_user_avatarfile']) ? $options['new_user_avatarfile'] : null);
                if($newUserAvatarFile && $newUserAvatarFile->isValid()){
                    
                    $oldAvatarFileName = $user->avatarfile;
                    $newUserAvatarFileName = $user->name . "_" . uniqid() . "." . $newUserAvatarFile->getClientOriginalExtension();
                    
                    $fileManager = new FileManager();
                    
                    $fileManager->saveAvatarFileAs($newUserAvatarFile, $newUserAvatarFileName);
                    $user->avatarfile = $newUserAvatarFileName;

                    $fileManager->deleteAvatarFile($oldAvatarFileName);
                }
                
                $user->save();

        }


        public function changePasswordForUser($user, $passwordData) {

                $newPassword = $passwordData["new_user_password"];
                $newPasswordConfirm = $passwordData["new_user_password_confirmation"];
                $password = $passwordData["user_password"];
                
                $this->checkPasswordData($user->email, $password, $newPassword, $newPasswordConfirm);
                
                $user->password = bcrypt($newPassword);
                $user->save();
                
        }
        
        
        public function checkUserCredentials($email, $password) {
            
                $attributes = [
                    "user_email" => $email,
                    "user_password" => [
                        $password,
                        "auth:" . $email
                    ],
                ];
                $this->validator->check($attributes, "LOGIN.ERROR");
            
        }
        
        
        public function registerNewUserWith($username, $email, $password, $passwordConfirmation) {
            
                $this->validator->check([
                    "new_user_name" => $username,
                    "new_user_email" => $email,
                    "new_user_password" => $password,
                    "new_user_password_confirmation" => $passwordConfirmation,
                ], "REGISTRATION.ERROR");
                
                return User::create([
                    'name' => $username,
                    'email' => $email,
                    'password' => bcrypt($password),
		]);
	
        }
        
        
        public function sendPasswordResetLink($email) {
            
		$this->validator->check(['user_email' => $email], "PASSWORDRESET.EMAIL.NOT.SENT");

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
            
		$this->validator->check([
			'password_reset_token' => $token,
			'user_email' => $email,
			'new_user_password' => $password,
			'new_user_password_confirmation' => $passwordConfirmation,
		], "PASSWORD.NOT.CHANGED");
                
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
    
}
