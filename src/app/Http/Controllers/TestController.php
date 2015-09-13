<?php namespace Game\Http\Controllers;

use Game\Managers\AccountManager;
use Game\Managers\MatchManager;
use Game\Managers\MessageManager;
use Game\User;
use Game\Handlers\Messages\SuccessFeedback;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;



class TestController extends Controller {
    
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

        
	public function perform() {
            
                $testThread = $this->messageManager->newThread("Thread", Auth::user(), [User::find(2), User::find(3)], false);
                $testMatch = $this->matchManager->createMatch("Match", "earth", 3, Auth::user(), $testThread, false);
                
                $this->matchManager->joinUserToMatch($testMatch, User::find(1), "blue");
                $this->matchManager->joinUserToMatch($testMatch, User::find(2), "red");
                $this->matchManager->joinUserToMatch($testMatch, User::find(3), "yellow");
                
                $templateImplementer = new \Game\Services\MapTemplateImplementer();
                $templateImplementer->implement("earth", $testMatch);
                
                $this->matchManager->distrubuteRegionsRandomlyToJoinedUsers($testMatch);
               
                $testMatch->continents->each(function($continent){
                    var_dump($continent->name . "[" . $continent->colorscheme . "]");
                    
                    $continent->regions->each(function($region){
                        var_dump($region->name);
                        
                        var_dump("Owner: " . $region->owner->name);
                        
                        $region->neighbors->each(function($neighbor){
                            var_dump("Neighbor: " . $neighbor->name);
                        });
                        
                    });
                });
                
	}

}
