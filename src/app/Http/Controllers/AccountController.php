<?php namespace Game\Http\Controllers;

use Game\Managers\AccountManager;
use Game\Managers\MatchManager;
use Game\Managers\MessageManager;
use Game\Managers\UserManager;
use Game\Handlers\Messages\SuccessFeedback;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;

use Game\User;



class AccountController extends Controller {
    
        protected $accountManager;
        protected $matchManager;
        protected $messageManager;
        protected $userManager;
        

        public function __construct(
                AccountManager $accountManager,
                MatchManager $matchManager,
                MessageManager $messageManager,
                UserManager $userManager) {
		
                $this->accountManager = $accountManager;
                $this->matchManager = $matchManager;
                $this->messageManager = $messageManager;
                $this->userManager = $userManager;
                
                $this->middleware('auth', ['except' => 'switchToLanguage']);
                
	}

        
	public function index() {
            
                $user = Auth::user();
                $unreadThreads = $this->messageManager->getUnreadThreadsForUser($user);
                //$matches = $this->matchManager->getAllPublicMatches();
                $matches = $this->matchManager->getAllMatches();
                
		return view('user.overview')
                        ->with("matches", $matches)
                        ->with("unreadThreads", $unreadThreads);
                
	}

        
        public function options() {
                
                $allowedThemes = $this->accountManager->getAllowedThemes();
		return view('user.options')
                        ->with("allowedThemes", $allowedThemes);
                
	}
        
        
        public function optionsSave() {
            
                $user = Auth::user();
                
                $newUserName = Input::get('new_user_name');
                $newUserEmail = Input::get('new_user_email');
                $newUserAvatarFile = Input::file('new_user_avatarfile');
                $newUserTheme = Input::get('new_user_theme');
                Log::info($newUserEmail);
                Log::info($newUserAvatarFile);
                Log::info($newUserTheme);
                
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
                if($user->csstheme !== $newUserTheme){
                    $attributes["new_user_theme"] = $newUserTheme;
                }
                $this->check($attributes, "INVALID.OPTIONS");
                
                $this->accountManager->setNameForUser($user, $newUserName);
                $this->accountManager->setEmailForUser($user, $newUserEmail);
                $this->accountManager->changeThemeForUser($user, $newUserTheme);
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

        
	public function profile($id = null) {
            
                $user = null;
                if($id){
                    $user = $this->userManager->getUserForId($id);
                }
                else{
                    $user = Auth::user();
                }
                return view('user.profile')
                    ->with("user", $user);
                
	}

}
