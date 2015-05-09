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
                $exceptNames = Request::input('selectednames');
                $exceptNames = str_replace(" ", "", $exceptNames);
                $ignoreNamesArray = explode(",", $exceptNames);
                $ignoreNamesArray[] = Auth::user()->name;
                
                Log::info("Searching not searching ");
                Log::info($ignoreNamesArray);
                $userNames = $this->jsonRestManager->getAllUserNamesLikeExcept(
                        Request::input('term'),
                        $ignoreNamesArray
                );
		return json_encode($userNames);
	}

}
