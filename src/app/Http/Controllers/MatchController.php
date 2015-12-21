<?php namespace Game\Http\Controllers;

use Game\Managers\MatchManager;
use Game\Managers\MessageManager;
use Game\Managers\UserManager;
use Game\Managers\AccountManager;
use Game\Handlers\Messages\SuccessFeedback;
use Game\Services\MapTemplateImplementer;
use Game\Model\Match;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

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

        
        
	public function createMatchForm() {
            
                $user = Auth::user();
                $this->matchManager->checkUserMayCreateMatch($user);
                
                $mapNames = $this->matchManager->getMapNames();
                return view('match.init')
                        ->with("mapNames", $mapNames);
                
	}

        
        
	public function createMatch() {
                
                $user = Auth::user();
                $matchName = trim((string) Input::get('match_name'));
                $mapName = (string) Input::get('match_map_name');
                $maxUsers = (int) Input::get('match_maximum_users');
                $isPublic = (boolean) Input::has('match_public');
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
                    "match_invited_users" => $isPublic | sizeof($invitedUsersArray),
                    "match_maximum_users" => $maxUsers
                ], "CREATE.MATCH.WRONG.PARAMETERS");

                $thread = $this->messageManager->newThread(
                        "Match '" . $matchName . "'",
                        $user,
                        $invitedUsersArray,
                        true
                );
                
                $match = $this->matchManager->createMatch(
                        $matchName,
                        $mapName,
                        $maxUsers,
                        $user,
                        $thread,
                        $isPublic
                );
                
                $mapTemplateImplementer = new MapTemplateImplementer();
                $mapTemplateImplementer->implement($mapName, $match);

                $this->messageManager->newMessage(
                        $thread,
                        $user,
                        $this->createJoinMatchThreadMessage($invitationMessage, $match)
                );

                return redirect()->route("match.join.init", $match->joinid);
                    
	}
        
        
        
        public function joinMatchForm($joinId) {
                    
                $user = Auth::user();
                $match = $this->matchManager->getMatchForJoinId($joinId);
                
                $this->matchManager->checkUserMayJoinMatch($user, $match);
                
                $untakenColorSchemes = $this->matchManager->getUntakenColorSchemesForMatch($match);
                
                return view('match.join')
                        ->with('match', $match)
                        ->with('colorSchemes', $untakenColorSchemes);
                    
        }
        
        
        
        public function joinMatch($joinId) {
            
                $user = Auth::user();
                $match = $this->matchManager->getMatchForJoinId($joinId);
                
                $this->matchManager->checkUserMayJoinMatch($user, $match);
                
                $colorScheme = (string) Input::get('match_user_colorscheme');
        
                $this->check([
                    "match_user_colorscheme" => [
                        $colorScheme,
                        "in:" . implode(",", $this->matchManager->getUntakenColorSchemesForMatch($match))
                    ]
                ], "JOIN.MATCH.WRONG.PARAMETERS");

                $this->matchManager->joinUserToMatch($match, $user, $colorScheme);
                
                $this->messageManager->newMessage(
                    $match->thread,
                    $user,
                    $user->name . " joined '". $match->name . "'"
                );
                
                return redirect()
                            ->route("match.goto");
                
        }
        
        
        
        public function startMatch() {
            
                $user = Auth::user();
                $match = $this->matchManager->getMatchForUser($user);
                $this->matchManager->checkUserCanAdministrateMatch($user, $match);
                
                $this->matchManager->startMatch($match);
                
                return redirect()
                            ->route("match.goto");
            
        }
        
        
        
        public function goToMatch() {
            
                $user = Auth::user();
                $match = $this->matchManager->getMatchForUser($user);
                
                if($match->state == MatchManager::MATCHSTATE_WAITING_FOR_PLAYERJOINS){
                    return view('match.waiting')->with('match', $match);
                } else if ($match->state == MatchManager::MATCHSTATE_STARTED){
                    $this->accountManager->setSocketJoinId($user);
                    return view('match.play')->with('match', $match);
                } else if ($match->state == MatchManager::MATCHSTATE_FINISHED){
                    $this->accountManager->setSocketJoinId($user);
                    return view('match.finished')->with('match', $match);
                }
            
        }
    
    
        
        public function administrateMatchForm() {
            
                $user = Auth::user();
                $match = $user->joinedMatch;
                $this->matchManager->checkUserCanAdministrateMatch($user, $match);
                $thread = $match->thread;
                return view("match.administrate")
                            ->with("match", $match)
                            ->with("thread", $thread);
                    
	}


        
        public function administrateMatchSave() {

                $user = Auth::user();
                $match = $user->joinedMatch;
                $this->matchManager->checkUserCanAdministrateMatch($user, $match);
                
                $maxUsers = (int) Input::get('match_maximum_users');
                $isPublic = Input::has('match_public');
                Log::info("Public: " . $isPublic);
                
                $this->check([
                    "match_maximum_users" => $maxUsers
                ], "ADMINISTRATE.MATCH.WRONG.PARAMETERS");
                
                $this->matchManager->changeMatchData($match, $maxUsers, $isPublic);
                
                return redirect()
                            ->back()
                            ->with("message", new SuccessFeedback("message.success.matchdata.save"));

        }
        
        
        
        public function inviteUsers() {

                $user = Auth::user();
                $match = $user->joinedMatch;
                $this->matchManager->checkUserCanAdministrateMatch($user, $match);
                
                $userNames = Input::get('match_invited_users');
                $userNameArray = explode(",", $userNames);
                $userArray = $this->userManager->findUsersForNames($userNameArray);
                
                $thread = $match->thread;
                
                $this->messageManager->addUsersToThread($user, $userArray, $thread);
                
                foreach($userArray as $invitedUser){
                    $this->messageManager->newMessage($thread, $invitedUser, $invitedUser->name . " invited.");
                }
            
                if(count($userArray)){
                    $this->messageManager->newMessage($thread, $user, $this->createJoinMatchThreadMessage("", $match));
                }
                
                return redirect()
                        ->back()
                        ->with("message", new SuccessFeedback("message.success.users.invited"));
            
        }

        
        
	public function cancelMatch() {
            
                $user = Auth::user();
                $match = $user->joinedMatch;
                $thread = $match->thread;
                $this->matchManager->checkUserCanDeleteMatch($user, $match);
                
                $this->matchManager->cancelMatch($match);
                
                if($thread){
                    $this->messageManager->newMessage($thread, $user, "Match " . $match->name . " was cancelled");
                }
                return redirect()->route("index")
                        ->with("message", new SuccessFeedback("message.success.match.cancelled"));

	}

        
        
	public function searchMatch() {
                
                $waitingMatches = $this->matchManager->getAllPublicWaitingMatches();
                return view("match.search")
                        ->with("matches", $waitingMatches);
                
        }
        
        
        public function removeMatchnotification() {
            
                $user = Auth::user();
                $user->matchnotification = null;
                $user->save();
            
        }
        
        
        protected function createJoinMatchThreadMessage($message, $match) {
            
                return  "<br>"
                        . '<a class="joinlink" href="/match/join/' . $match->joinid . '">'
                        . '<img src="/img/matches.png"> '
                        . "Join '" . $match->name . "'"
                        . "</a>"
                        . "<br>"
                        . "$message";
            
        }

}
