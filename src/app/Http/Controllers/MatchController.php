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
use \Game\Model\UserJoinMatch;

class MatchController extends Controller {
    
        
        protected $matchManager;

        
        public function __construct(MatchManager $matchManager)
	{
		$this->middleware('auth');
                $this->matchManager = $matchManager;
	}

	public function index()
	{
                $matches = $this->matchManager->getMatches();
                $user = Auth::user();
                $invitations = UserJoinMatch::where("user_id", "=", Auth::user()->id)
                        ->where("status", "=", "invited")->get();
                $rejectedInvitations = UserJoinMatch::where("invited_by_user_id", "=", Auth::user()->id)
                        ->where("status", "=", "rejected")->get();
		return view('match.overview')
                        ->with("matches", $matches)
                        ->with("user", $user)
                        ->with("invitations", $invitations)
                        ->with("rejectedInvitations", $rejectedInvitations);
	}

	public function init()
	{
                try{
                    $this->matchManager->checkUserCanCreateMatch(Auth::user());
                } catch (GameException $ex) {
                    
                    return redirect()
                            ->back()
                            ->with("message", new ErrorFeedback($ex->getUIMessageKey()));
                }
                return view('match.init');
	}

	public function create()
	{
                try {
                    
                    $this->matchManager->checkUserCanCreateMatch(Auth::user());
                    $this->matchManager->createMatch(Auth::user(), [
                        "invited_players" => Request::input('invited_players'),
                        "name" => Request::input('name'),
                        "invitation_message" => Request::input('invitation_message')
                    ]);
                    return redirect("index");
                    
                } catch (GameException $ex) {
                    
                    return redirect()
                                ->back()
                                ->with("message", new ErrorFeedback($ex->getUIMessageKey()));
                    
                }
                return view('match.init');
	}

	public function cancel($id)
	{
                try {
                    $user = Auth::user();
                    $this->matchManager->checkUserCanDeleteMatch($id, $user);
                    $this->matchManager->deleteMatch($id, $user);
                    return $this->index();
                    
                } catch (GameException $ex) {
                    
                    return redirect()
                                ->back()
                                ->with("message", new ErrorFeedback($ex->getUIMessageKey()));
                    
                }
	}
        
        public function match($id)
        {
                try {
                    
                    $user = Auth::user();
                    $match = $this->matchManager->goToMatch((int)$id, $user);
                    return view('match.play')->with('match', $match);
                    
                } catch (GameException $ex) {
                    
                    return redirect()
                                ->back()
                                ->with("message", new ErrorFeedback($ex->getUIMessageKey()));
                    
                }
        }
        
        public function rejectInvitation($id)
        {
                try {
                    
                    $user = Auth::user();
                    $this->matchManager->rejectInvitation((int)$id, $user);
                    return redirect()
                                ->back()
                                ->with("message", new WarnFeedback("message.warning.invitation.rejected"));
                    
                } catch (GameException $ex) {
                    
                    return redirect()
                                ->back()
                                ->with("message", new ErrorFeedback($ex->getUIMessageKey()));
                    
                }
        }
        
        public function deleteInvitation($id)
        {
                try {
                    
                    $user = Auth::user();
                    $this->matchManager->deleteInvitation((int)$id, $user);
                    return redirect()
                                ->back()
                                ->with("message", new SuccessFeedback("message.success.invitation.deleted"));
                    
                } catch (GameException $ex) {
                    
                    return redirect()
                                ->back()
                                ->with("message", new ErrorFeedback($ex->getUIMessageKey()));
                    
                }
        }
    
    
        public function administrate($id)
	{
                try {
                    $user = Auth::user();
                    $match = Match::find($id);
                    $this->matchManager->checkUserCanAdministrateMatch($match, $user);
                    return view("match.administrate")
                                ->with("match", $match);
                    
                } catch (GameException $ex) {
                    
                    return redirect()
                                ->back()
                                ->with("message", new ErrorFeedback($ex->getUIMessageKey()));
                }
	}


        public function saveAdministrate($id) {

                try {
                    $user = Auth::user();
                    $match = Match::find($id);
                    if($match){
                        $this->matchManager->checkUserCanAdministrateMatch($match, $user);
                        $this->matchManager->saveAdministratedMatch($match, $user, [
                            "invited_players" => Request::input('invited_players'),
                            "invitation_message" => Request::input('invitation_message')
                        ]);
                    }
                    return view("match.administrate")
                                ->with("match", $match)
                                ->with("message", new SuccessFeedback("message.success.matchdata.save"));
                    
                } catch (GameException $ex) {
                    
                    return redirect()
                                ->back()
                                ->with("message", new ErrorFeedback($ex->getUIMessageKey()));
                }
        }
        
        
        public function cancelMatch($id) {
            
            return redirect("index")->with("message", new SuccessFeedback("Cancelled"));
            
        }

}
