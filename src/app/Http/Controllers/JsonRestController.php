<?php namespace Game\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Game\Managers\UserManager;

class JsonRestController extends Controller {
    
        protected $userManager;


        public function __construct(UserManager $userManager) {
            
                $this->userManager = $userManager;
		$this->middleware('auth');
                
	}

	public function allUsersExceptCurrentUser() {
            
                $users = $this->userManager->getAllUsers(Auth::user()->name);
                \Illuminate\Support\Facades\Log::info($users->first());
		return json_encode($users);
                
	}

}
