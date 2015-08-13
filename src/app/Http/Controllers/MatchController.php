<?php namespace Game\Http\Controllers;

use Game\Model\Match;
use Game\Managers\MatchManager;
use Game\Managers\MessageManager;
use Game\Managers\UserManager;
use Game\Managers\AccountManager;
use Game\Services\PolicyComplianceService;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class MatchController extends Controller {
    
        
        protected $matchManager;
        protected $messageManager;
        protected $userManager;
        protected $accountManager;

        
        public function __construct(
                MatchManager $matchManager,
                MessageManager $messageManager,
                UserManager $userManager,
                AccountManager $accountManager)
        {
            
                $this->matchManager = $matchManager;
                $this->messageManager = $messageManager;
                $this->userManager = $userManager;
                $this->accountManager = $accountManager;
                
		$this->middleware('auth');
	}

        
        
	public function init() {
            
                $user = Auth::user();
                $this->matchManager->checkUserMayCreateMatch($user);
                $mapNames = $this->matchManager->getMapNames();
                return view('match.init')
                        ->with("mapNames", $mapNames);
                
	}

        
        
	public function create() {
                
                $user = Auth::user();
                $matchName = trim((string) Input::get('match_name'));
                $mapName = (string) Input::get('match_map_name');
                $maxUsers = (int) Input::get('match_maximum_users');
                $invitationMessage = trim((string) Input::get("message"));
                $userNameArray = explode(",", (string) Input::get("match_invited_users"));
                $invitedUsersArray = $this->userManager->findUsersForNames($userNameArray);
                
                $this->matchManager->checkUserMayCreateMatch($user);
        
                $this->check([
                    "match_name" => $matchName,
                    "match_map_name" => [
                        $mapName,
                        "in:" . implode(",", $this->matchManager->getMapNames())
                    ],
                    "match_invited_users" => sizeof($invitedUsersArray),
                    "match_maximum_users" => $maxUsers
                ], "CREATE.MATCH.WRONG.PARAMETERS");
                
                $match = $this->matchManager->createMatch(
                        $matchName,
                        $mapName,
                        $maxUsers,
                        $user
                );

                $thread = $this->messageManager->newThread(
                        "Match '" . $match->name . "'",
                        $user,
                        $invitedUsersArray,
                        true
                );
                
                $match->thread()->associate($thread);
                $match->save();

                $this->messageManager->newMessage(
                        $thread,
                        $user,
                        $this->createJoinMatchThreadMessage($invitationMessage, $match)
                );

                $this->matchManager->joinUserToMatch($match, $user);

                return redirect()->route("index");
                    
	}
        
        
        
        public function joinInit($joinId) {
                    
                $user = Auth::user();
                $this->matchManager->checkUserMayJoinMatch($user);
                
                $match = $this->matchManager->getMatchForJoinId($joinId);
                return view('match.join')->with('match', $match);
                    
        }
        
        
        
        public function joinConfirm($joinId) {
            
                $user = Auth::user();
                $this->matchManager->checkUserMayJoinMatch($user);
                
                $match = $this->matchManager->getMatchForJoinId($joinId);
                $this->matchManager->joinUserToMatch($match, $user);
                return redirect()
                            ->route("match.goto");

        }

        
        
	public function cancel($id) {
            
                $user = Auth::user();                
                $match = $this->matchManager->getMatchForId($id);
                $this->matchManager->checkUserCanDeleteMatch($user, $match);

                $this->matchManager->cancelMatch($match);
                
                $thread = $match->thread;
                if($thread){
                    $this->messageManager->newMessage($thread, $user, "Match " . $match->name . " was cancelled");
                }
                return redirect("index")
                        ->with("message", new SuccessFeedback("message.success.match.cancelled"));

	}
        
        
        
        public function goToMatch() {
            
                $user = Auth::user();
                $match = $this->matchManager->getMatchForUser($user);
                $this->accountManager->setSocketJoinId($user);
                
                return view('match.play')->with('match', $match);
            
        }
    
    
        
        public function administrate($id) {
            
                $user = Auth::user();
                $match = $this->matchManager->getMatchForId($id);
                $this->matchManager->checkUserCanAdministrateMatch($user, $match);
                return view("match.administrate")
                            ->with("match", $match);
                    
	}


        
        public function saveAdministrate($id) {

                $user = Auth::user();
                $match = $this->matchManager->getMatchForId($id);
                $this->matchManager->checkUserCanAdministrateMatch($user, $match);
                $this->matchManager->saveAdministratedMatch($match, $user, [
                    "invited_players" => Input::get('invited_players'),
                    "message" => Input::get('message')
                ]);
                
                return view("match.administrate")
                            ->with("match", $match)
                            ->with("message", new SuccessFeedback("message.success.matchdata.save"));

        }
        
        
        protected function createJoinMatchThreadMessage($message, $match) {
            
                return  "<br>"
                        . '<a class="joinlink" target="_blank" href="/match/join/' . $match->joinid . '\">'
                        . '<img src="/img/matches.png"> '
                        . "Join '" . $match->name . "'"
                        . "</a>"
                        . "<br>"
                        . "$message";
            
        }

}
