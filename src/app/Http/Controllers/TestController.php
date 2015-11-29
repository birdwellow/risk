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
            
            /*$hits = array();
            
            $testString = "[regions:id=3]";
            $regex = '/\[(.*?):(.*?)=(.*?)\]/';
            $result = preg_match($regex, $testString, $hits);
            unset($hits[0]);
            
            var_dump($hits);
            echo $result;
            
            $obj = new \stdClass();
            
            echo ($obj->test instanceof \stdClass);*/
            
            $points = 
                [20,20,80,80]
            //    [120,120,280,280],
            ;
            
            $conn = new \Game\Model\Connection();
            $conn->points = json_encode($points);
            $conn->save();
            
            var_dump(json_encode($points));
              
	}

}
