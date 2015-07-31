<?php namespace Game\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Game\Managers\UserManager;

class JsonRestController extends Controller {
    
        protected $userManager;


        public function __construct(UserManager $userManager) {
            
                $this->userManager = $userManager;
		$this->middleware('auth');
                
	}

	public function allUserNamesExceptCurrentUser() {
            
                $userNames = $this->userManager->getAllUserNames([Auth::user()->name]);
		return $userNames;
                
	}

}
