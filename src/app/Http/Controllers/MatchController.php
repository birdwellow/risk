<?php namespace Game\Http\Controllers;

use \Illuminate\Support\Facades\Request;
use \Game\Model\Match;
use \Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\Log;
use Game\User;
use Game\Managers\MatchManager;
use Game\Exceptions\GameException;
use Game\Handlers\Messages\ErrorFeedback;

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
		return view('match.overview')->with("matches", $matches);
	}

	public function overview()
	{
                return $this->index();
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
                        "name" => Request::input('name')
                    ]);
                    return $this->overview();
                    
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
                    return $this->overview();
                    
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

}
