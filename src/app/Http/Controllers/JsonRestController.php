<?php namespace Game\Http\Controllers;

use \Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\Log;
use Game\User;
use Illuminate\Support\Facades\Request;
use Game\Managers\JsonRestManager;

class JsonRestController extends Controller {
    
        protected $jsonRestManager;


        public function __construct(JsonRestManager $jsonRestManager)
	{
                $this->jsonRestManager = $jsonRestManager;
		$this->middleware('auth');
	}

	public function allUserNames()
	{
                $userNames = $this->jsonRestManager->getAllUserNamesLikeExcept(
                        Request::input('term'),
                        array(Auth::user())
                );
		return json_encode($userNames);
	}

}
