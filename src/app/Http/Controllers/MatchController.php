<?php namespace Game\Http\Controllers;

use \Illuminate\Support\Facades\Request;
use \Game\Model\Match;
use \Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\Log;
use Game\User;
use Game\Managers\MatchManager;
use Game\Exceptions\GameException;
use Game\Handlers\Messages\ErrorFeedback;
use Game\Handlers\Messages\WarnFeedback;
use Game\Handlers\Messages\SuccessFeedback;
use \Game\Model\Invitation;

class MatchController extends Controller {
    
        
        protected $matchManager;

        
        public function __construct(MatchManager $matchManager) {
            
		$this->middleware('auth');
                $this->matchManager = $matchManager;
	}

        
        
	public function index() {
            
                $matches = $this->matchManager->getMatches();
                $user = Auth::user();
                $invitations = $this->matchManager->getNewInvitationsForUser($user->id);
                $rejectedInvitations = $this->matchManager->getRejectedInvitationsForUser($user->id);
		return view('match.overview')
                        ->with("matches", $matches)
                        ->with("user", $user)
                        ->with("invitations", $invitations)
                        ->with("rejectedInvitations", $rejectedInvitations);
	}

        
        
	public function init() {
                $this->matchManager->checkUserCanCreateMatch(Auth::user());
                $mapNames = $this->matchManager->getMapNames();
                return view('match.init')
                        ->with("mapNames", $mapNames);
                
	}

        
        
	public function create() {
                    
                $this->matchManager->checkUserCanCreateMatch(Auth::user());
                $match = $this->matchManager->createMatch(Auth::user(), [
                    "invited_players" => Request::input('invited_players'),
                    "mapName" => Request::input('mapName'),
                    "name" => Request::input('name'),
                    "message" => Request::input('message'),
                    "closed" => Request::input('closed'),
                    "maxusers" => Request::input('maxusers')
                ]);

                return redirect()->route("match.join.confirm", $match->id);
                    
	}

        
        
	public function cancel($id) {
            
                $user = Auth::user();
                $this->matchManager->checkUserCanDeleteMatch($id, $user);
                $this->matchManager->deleteMatch($id, $user);
                return $this->index();

	}
        
        
        
        public function joinInit($matchId) {
                    
                $user = Auth::user();
                $this->matchManager->checkUserMayJoinMatch($user->id, $matchId);
                $match = Match::find($matchId);
                return view('match.join')->with('match', $match);
                    
        }
        
        
        
        public function joinConfirm($matchId) {
            
                $user = Auth::user();
                $this->matchManager->checkUserMayJoinMatch($user->id, $matchId);
                $this->matchManager->joinMatch((int)$matchId, $user);
                return redirect()
                            ->route("match.goto");

        }
        
        
        
        public function goToMatch() {
            
                $user = Auth::user();
                $match = $this->matchManager->goToMatch((int)$user->joinedMatch->id, $user);
                return view('match.play')->with('match', $match);
            
        }
        
        
        
        public function rejectInvitation($id) {
                    
                $user = Auth::user();
                $this->matchManager->rejectInvitation((int)$id, $user);
                return redirect()
                            ->back()
                            ->with("message", new WarnFeedback("message.warning.invitation.rejected"));

        }
        
        
        
        public function deleteInvitation($id) {
                    
                $user = Auth::user();
                $this->matchManager->deleteInvitation((int)$id, $user);
                return redirect()
                            ->back()
                            ->with("message", new SuccessFeedback("message.success.invitation.deleted"));

        }
    
    
        
        public function administrate($id) {
                $user = Auth::user();
                $match = Match::find($id);
                $this->matchManager->checkUserCanAdministrateMatch($match, $user);
                return view("match.administrate")
                            ->with("match", $match);
                    
	}


        
        public function saveAdministrate($id) {

                $user = Auth::user();
                $match = Match::find($id);
                if($match){
                    $this->matchManager->checkUserCanAdministrateMatch($match, $user);
                    $this->matchManager->saveAdministratedMatch($match, $user, [
                        "invited_players" => Request::input('invited_players'),
                        "message" => Request::input('message')
                    ]);
                }
                return view("match.administrate")
                            ->with("match", $match)
                            ->with("message", new SuccessFeedback("message.success.matchdata.save"));

        }
        
        
        
        public function cancelMatch($id) {
            
                return redirect("index")->with("message", new SuccessFeedback("Cancelled"));
            
        }

}
