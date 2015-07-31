<?php namespace Game\Managers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

use Game\Services\PolicyComplianceService;

/**
 * Description of MatchManager
 *
 * @author fvo
 */
class AccountManager {
    
        protected $validator;

        protected $sessionLangToken = "language";
        protected $fallbackLocale = "en";
        protected $allowedLocales = ["en", "de"];
        
        
        public function __construct(PolicyComplianceService $validator) {
                
                $this->validator = $validator;
                
        }
        
        
        protected function checkUserOptionsForUser($user, $userName, $userEmail){
            
                $attributes = array();
                if($user->name !== $userName){
                    $attributes["username"] = $userName;
                }
                if($user->email !== $userEmail){
                    $attributes["email"] = $userEmail;
                }
                
                $this->validator->check($attributes, "USER.INVALID.OPTIONS");
            
        }
        
        
        protected function checkPasswordData($email, $oldPassword, $newPassword, $newPasswordConfirm){
                
                $attributes = [
                    "old.password" => [
                        $oldPassword,
                        "auth:" . $email
                    ],
                    "new.password" => $newPassword,
                    "new.password_confirmation" => $newPasswordConfirm,
                ];
                
                $this->validator->check($attributes, "USER.PASSWORDNOTCHANGED");
            
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


        public function setAppLocale($user) {
            
                if($user && $user->language){
                    Session::set($this->sessionLangToken, $user->language);
                }

                if(!Session::get($this->sessionLangToken)){
                    Session::set($this->sessionLangToken, $this->fallbackLocale);
                }

                App::setLocale(Session::get($this->sessionLangToken));
                
        }

        
        public function changeOptionsForUser($user, $options){

                $newUserName = $options['name'];
                $newUserEmail = $options['email'];
                
                $this->checkUserOptionsForUser($user, $newUserName, $newUserEmail);
                
                $user->name = $newUserName;
                $user->email = $newUserEmail;

                $newUserAvatarFile = ( isset($options['avatar']) ? $options['avatar'] : null);
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

                $newPassword = $passwordData["newpassword"];
                $newPasswordConfirm = $passwordData["newpasswordconfirm"];
                $oldPassword = $passwordData["oldpassword"];
                
                $this->checkPasswordData($user->email, $oldPassword, $newPassword, $newPasswordConfirm);
                
                $user->password = bcrypt($newPassword);
                $user->save();
                
        }
    
    
}
