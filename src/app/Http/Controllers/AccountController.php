<?php namespace Game\Http\Controllers;

use Game\Managers\AccountManager;
use Game\Managers\MatchManager;
use Game\Managers\MessageManager;
use Game\Handlers\Messages\SuccessFeedback;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;



class AccountController extends Controller {
    
        protected $accountManager;
        protected $matchManager;
        protected $messageManager;
        

        public function __construct(
                AccountManager $accountManager,
                MatchManager $matchManager,
                MessageManager $messageManager) {
		
                $this->accountManager = $accountManager;
                $this->matchManager = $matchManager;
                $this->messageManager = $messageManager;
                
                $this->middleware('auth');
                
	}

        
	public function index() {
            
                $user = Auth::user();
                $unreadThreads = $this->messageManager->getUnreadThreadsForUser($user);
                $matches = $this->matchManager->getAllPublicMatches();
                
		return view('user.overview')
                        ->with("matches", $matches)
                        ->with("unreadThreads", $unreadThreads);
                
	}

        
        public function options() {
            
		return view('user.options');
                
	}
        
        
        public function optionsSave() {
            
                $user = Auth::user();
                
                $newUserName = Input::get('new_user_name');
                $newUserEmail = Input::get('new_user_email');
                $newUserAvatarFile = Input::file('new_user_avatarfile');
                Log::info($newUserEmail);
                Log::info($newUserAvatarFile);
                
                $attributes = array();
                if($user->name !== $newUserName){
                    $attributes["new_user_name"] = $newUserName;
                }
                if($user->email !== $newUserEmail){
                    $attributes["new_user_email"] = $newUserEmail;
                }
                if($newUserAvatarFile){
                    $attributes["new_user_avatarfile"] = $newUserAvatarFile;
                }
                $this->check($attributes, "INVALID.OPTIONS");
                
                $this->accountManager->setNameForUser($user, $newUserName);
                $this->accountManager->setEmailForUser($user, $newUserEmail);
                if($newUserAvatarFile){
                    $this->accountManager->setAvatarFileForUser($user, $newUserAvatarFile);
                }

                return redirect()->back()->with(
                        "message",
                        new SuccessFeedback("message.success.userinput.save")
                );
        }
        
        
        public function passwordSave() {
            
                $user = Auth::user();
                
                $password = Input::get("user_password");
                $newPassword = Input::get("new_user_password");
                $newPasswordConfirm = Input::get("new_user_password_confirmation");
                
                $attributes = [
                    "user_password" => [
                        $password,
                        "auth:" . $user->email
                    ],
                    "new_user_password" => $newPassword,
                    "new_user_password_confirmation" => $newPasswordConfirm,
                ];
                $this->check($attributes, "PASSWORD.NOT.CHANGED");
                
                $this->accountManager->setPasswordForUser($user, $password);

                return redirect()->back()->with(
                        "message",
                        new SuccessFeedback("message.success.password.changed")
                );
            
        }

        
	public function switchToLanguage($lang) {
            
                if(Auth::user()){
                    $this->accountManager->changeLanguageForUser(Auth::user(), $lang);
                } else {
                    $this->accountManager->changeLanguageForSession($lang);
                }
                return redirect()->back();
                
	}

}
