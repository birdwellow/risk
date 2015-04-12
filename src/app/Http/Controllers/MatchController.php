<?php namespace Game\Http\Controllers;

use \Illuminate\Support\Facades\Request;
use \Game\Model\Match;
use \Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\Log;

class MatchController extends Controller {
    
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function index()
	{
                $matches = Match::all();
		return view('match.overview')->with("matches", $matches);
	}

	public function overview()
	{
                return $this->index();
	}

	public function init()
	{
                $user = Auth::user();
                if($user->joinedMatch !== null){
                    $dialog = [
                        "type" => "error",
                        "message" => "You already joined a match",
                        "title" => "Not allowed"
                    ];
                    Log::info($dialog);
                    return $this->overview()
                            ->with("dialog", $dialog);
                }
                return view('match.init');
	}

	public function create()
	{
                $user = Auth::user();
                if($user->joinedMatch !== null){
                    return redirect("/home");
                }
                $match = new Match();
                $match->name = Request::input('name');
                $match->createdBy()->associate(Auth::user());
                $match->save();
                return $this->overview();
	}

	public function cancel($id)
	{
                $match = Match::find($id);
                if($match && Auth::user()->id == $match->createdBy->id){
                    $match->delete();
                }
                return $this->overview();
	}
        
        public function match($id)
        {
                $id = (int)$id;
                $user = Auth::user();
                if($user->joinedMatch && $user->joinedMatch->id !== $id){
                    return redirect("/home");
                }
                $match = Match::find($id);
                if($match == null){
                    return redirect("/home");
                } else {
                    $user->joinedMatch()->associate($match);
                    $user->save();
                    return view('match.play')->with('match', $match);
                }
        }

}
