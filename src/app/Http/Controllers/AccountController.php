<?php namespace Game\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

use Game\Managers\AccountManager;
use Game\Managers\MatchManager;
use Game\Managers\MessageManager;
use Game\Handlers\Messages\SuccessFeedback;



class AccountController extends Controller {
    
        protected $accountManager;
        protected $matchManager;
        protected $messageManager;


        public function __construct(AccountManager $accountManager, MatchManager $matchManager, MessageManager $messageManager) {
		
                $this->middleware('auth');
                $this->accountManager = $accountManager;
                $this->matchManager = $matchManager;
                $this->messageManager = $messageManager;
                
	}

        
	public function index() {
            
                $user = Auth::user();
                $unreadThreads = $this->messageManager->getUnreadThreadsForUser($user);
                $matches = $this->matchManager->getAllMatches();
                
		return view('user.overview')
                        ->with("matches", $matches)
                        ->with("unreadThreads", $unreadThreads);
                
	}

        
        public function options() {
            
		return view('user.options');
                
	}
        
        
        public function optionsSave() {
            
                $user = Auth::user();
                $options = Request::all();
                    
                $this->accountManager->changeOptionsForUser($user, $options);

                return redirect()->back()->with(
                        "message",
                        new SuccessFeedback("message.success.userinput.save")
                );
        }
        
        
        public function passwordSave() {
            
                $user = Auth::user();
                $passwordInputs = Request::all();
                  
                $this->accountManager->changePasswordForUser($user, $passwordInputs);

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
